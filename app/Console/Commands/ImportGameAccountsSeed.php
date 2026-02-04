<?php

namespace App\Console\Commands;

use App\Models\GameAccount;
use App\Models\User;
use App\XileRetro\XileRetro_DonationUbers;
use App\XileRetro\XileRetro_Login;
use App\XileRO\XileRO_Login;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ImportGameAccountsSeed extends Command
{
    protected $signature = 'player:import
                            {--server=xilero : Server to import from (xilero or xileretro)}
                            {--account-id= : Import a specific account by ID}
                            {--all : Import all accounts that are not yet linked}
                            {--sync-ubers : Sync legacy_uber_balance for all existing game accounts}
                            {--limit= : Limit total number of accounts to import}
                            {--batch=500 : Batch size for processing large datasets}
                            {--skip-existing-check : Skip checking if GameAccount already exists (faster)}
                            {--dry-run : Show what would be imported without making changes}';

    protected $description = 'Import game accounts from XileRO_Login/XileRetro_Login and sync legacy uber balances';

    /**
     * System emails that should be completely skipped (no import at all).
     */
    protected array $systemEmails = [
        'athena@athena.com',
    ];

    /**
     * Check if an email is a system email that should be skipped entirely.
     */
    protected function isSystemEmail(string $email): bool
    {
        return in_array(strtolower($email), $this->systemEmails);
    }

    /**
     * Check if an email is valid for creating a master account.
     * Fake emails will still get a GameAccount, but no User.
     */
    protected function isValidEmail(string $email): bool
    {
        if (empty($email)) {
            return false;
        }

        // Default placeholder emails
        if ($email === 'a@a.com') {
            return false;
        }

        // Auto-generated pattern: {numbers}@a.com
        if (preg_match('/^\d+@a\.com$/i', $email)) {
            return false;
        }

        // Auto-generated pattern: {numbers}@xilero.net
        if (preg_match('/^\d+@xilero\.net$/i', $email)) {
            return false;
        }

        // Auto-generated pattern: {anything}@a.com variants
        if (preg_match('/@a\.com$/i', $email) && ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Basic email validation
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function handle(): int
    {
        $server = $this->option('server');
        $accountId = $this->option('account-id');
        $importAll = $this->option('all');
        $syncUbers = $this->option('sync-ubers');
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        $dryRun = $this->option('dry-run');

        if (! in_array($server, ['xilero', 'xileretro'])) {
            $this->error('Invalid server. Use --server=xilero or --server=xileretro');

            return Command::FAILURE;
        }

        $loginClass = $server === 'xileretro' ? XileRetro_Login::class : XileRO_Login::class;
        $serverName = $server === 'xileretro' ? 'XileRetro' : 'XileRO';

        $this->info("Processing {$serverName} ({$loginClass})...");
        $this->newLine();

        // Sync legacy uber balances for existing accounts
        if ($syncUbers) {
            return $this->syncLegacyUberBalances($server, $dryRun, $limit);
        }

        if ($accountId) {
            return $this->importSingleAccount($loginClass, $server, (int) $accountId, $dryRun);
        }

        if ($importAll) {
            return $this->importAllAccounts($loginClass, $server, $dryRun, $limit);
        }

        // Interactive mode - show list and let user choose
        return $this->interactiveImport($loginClass, $server, $dryRun);
    }

    protected function importSingleAccount(string $loginClass, string $server, int $accountId, bool $dryRun): int
    {
        $login = $loginClass::find($accountId);

        if (! $login) {
            $this->error("Account ID {$accountId} not found.");

            return Command::FAILURE;
        }

        // Skip system accounts entirely
        if ($this->isSystemEmail($login->email)) {
            $this->error("Account {$login->userid} has system email ({$login->email}) and cannot be imported.");

            return Command::FAILURE;
        }

        // Check if already imported
        $existing = GameAccount::where('ragnarok_account_id', $accountId)
            ->where('server', $server)
            ->first();

        if ($existing) {
            $userInfo = $existing->user ? $existing->user->email : 'unclaimed';
            $this->warn("Account {$login->userid} (ID: {$accountId}) is already imported (master account: {$userInfo})");

            return Command::SUCCESS;
        }

        $hasValidEmail = $this->isValidEmail($login->email);

        if ($dryRun) {
            $this->info('[DRY RUN] Would import:');
            $this->displayLoginInfo($login, $hasValidEmail, $server);

            return Command::SUCCESS;
        }

        $result = $this->createAccountsFromLogin($login, $server);

        if ($result) {
            $this->info('Account imported successfully!');
            $this->displayResult($result['user'], $result['gameAccount'], $result['legacyUbers'] ?? 0);

            return Command::SUCCESS;
        }

        return Command::FAILURE;
    }

    protected function importAllAccounts(string $loginClass, string $server, bool $dryRun, ?int $limit = null): int
    {
        $batchSize = (int) $this->option('batch');

        // Count total accounts first
        $this->info('Counting accounts in game database...');

        $baseQuery = $loginClass::where('group_id', '<', 99) // Exclude admin accounts
            ->whereNotIn('email', $this->systemEmails); // Exclude system accounts

        $totalCount = (clone $baseQuery)->count();

        if ($totalCount === 0) {
            $this->info('No accounts found in game database.');

            return Command::SUCCESS;
        }

        $processCount = $limit ? min($limit, $totalCount) : $totalCount;
        $limitInfo = $limit ? " (limited to {$limit})" : '';
        $this->info("Found {$totalCount} accounts in game database{$limitInfo}.");
        $this->info("Will process in batches of {$batchSize}.");
        $this->newLine();

        if ($dryRun) {
            $this->info('[DRY RUN] Sample accounts from game database (first 20):');
            $this->newLine();

            $sample = (clone $baseQuery)->limit(20)->get();

            if ($server === 'xileretro') {
                $tableData = $sample->map(fn ($login) => [
                    $login->account_id,
                    $login->userid,
                    $login->email,
                    $login->sex,
                    $this->isValidEmail($login->email) ? 'Yes' : 'No',
                    XileRetro_DonationUbers::getTotalUbersForAccount($login->account_id),
                ])->toArray();

                $this->table(['Account ID', 'Username', 'Email', 'Sex', 'Master Account?', 'Legacy Ubers'], $tableData);
            } else {
                $tableData = $sample->map(fn ($login) => [
                    $login->account_id,
                    $login->userid,
                    $login->email,
                    $login->sex,
                    $this->isValidEmail($login->email) ? 'Yes' : 'No',
                ])->toArray();

                $this->table(['Account ID', 'Username', 'Email', 'Sex', 'Master Account?'], $tableData);
            }

            if ($processCount > 20) {
                $this->newLine();
                $this->info('... and '.($processCount - 20).' more accounts.');
            }

            return Command::SUCCESS;
        }

        if (! $this->confirm('Import new accounts (processing newest first, stops after 500 consecutive already-synced)?')) {
            $this->info('Import cancelled.');

            return Command::SUCCESS;
        }

        $imported = 0;
        $skipped = 0;
        $withMaster = 0;
        $withoutMaster = 0;
        $failed = 0;
        $totalLegacyUbers = 0;
        $pendingLegacyUbers = 0;
        $processed = 0;
        $consecutiveSynced = 0;
        $maxConsecutiveSynced = 500;

        $query = clone $baseQuery;
        if ($limit) {
            $query->limit($limit);
        }

        $this->info('Processing accounts (newest first)...');

        foreach ($query->orderBy('account_id', 'desc')->cursor() as $login) {
            try {
                $result = $this->createAccountsFromLogin($login, $server);
                $processed++;

                if ($result['wasCreated'] ?? true) {
                    $imported++;
                    $consecutiveSynced = 0; // Reset counter on new import
                    $legacyUbers = $result['legacyUbers'] ?? 0;

                    if ($result['user']) {
                        $withMaster++;
                        $totalLegacyUbers += $legacyUbers;
                    } else {
                        $withoutMaster++;
                        $pendingLegacyUbers += $legacyUbers;
                    }
                } else {
                    $skipped++;
                    $consecutiveSynced++;
                }

                // Show progress every record
                $this->output->write("\r  Processed: {$processed} | Imported: {$imported} | Skipped: {$skipped} | Consecutive synced: {$consecutiveSynced}/{$maxConsecutiveSynced}");

                // Stop if we hit 100 consecutive already-synced accounts
                if ($consecutiveSynced >= $maxConsecutiveSynced) {
                    $this->newLine();
                    $this->info("Reached {$maxConsecutiveSynced} consecutive already-synced accounts. All new accounts imported.");
                    break;
                }
            } catch (Exception $e) {
                $failed++;
                $processed++;
                $this->error("  Failed {$login->account_id}: {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info("Import complete: {$imported} imported ({$withMaster} with master account, {$withoutMaster} unclaimed), {$skipped} skipped (already exist), {$failed} failed.");

        if ($server === 'xileretro' && ($totalLegacyUbers > 0 || $pendingLegacyUbers > 0)) {
            $this->info("Legacy ubers: {$totalLegacyUbers} transferred to master accounts, {$pendingLegacyUbers} pending (on unclaimed accounts).");
        }

        // Sync legacy uber balances for XileRetro
        if ($server === 'xileretro') {
            $this->newLine();
            $this->syncAllLegacyUbers();
        }

        return Command::SUCCESS;
    }

    protected function syncAllLegacyUbers(): void
    {
        $this->info('Syncing legacy uber balances from donation_ubers...');

        $uberRecords = XileRetro_DonationUbers::where(function ($q) {
            $q->where('current_ubers', '>', 0)
                ->orWhere('pending_ubers', '>', 0);
        })->get();

        $this->info("Found {$uberRecords->count()} accounts with legacy ubers in donation_ubers.");

        $updated = 0;
        $notFound = 0;
        $skipped = 0;
        $accountsToTransfer = [];

        foreach ($uberRecords as $uberRecord) {
            $totalUbers = $uberRecord->total_ubers;

            $gameAccount = GameAccount::where('server', 'xileretro')
                ->where('ragnarok_account_id', $uberRecord->account_id)
                ->first();

            if (! $gameAccount) {
                $notFound++;

                continue;
            }

            // Skip accounts that already transferred ubers (has user + legacy_uber_balance = 0)
            if ($gameAccount->user_id && $gameAccount->legacy_uber_balance == 0) {
                $skipped++;

                continue;
            }

            // Update legacy_uber_balance for accounts without master
            if (! $gameAccount->user_id) {
                if ($gameAccount->legacy_uber_balance != $totalUbers) {
                    $gameAccount->update(['legacy_uber_balance' => $totalUbers]);
                    $updated++;
                }
            } else {
                // Has master - track for transfer
                $accountsToTransfer[$gameAccount->id] = [
                    'gameAccount' => $gameAccount,
                    'totalUbers' => $totalUbers,
                ];
            }

            $this->output->write("\r  Updated: {$updated} | Skipped: {$skipped} | Not found: {$notFound}");
        }

        $this->newLine();
        $this->info("Legacy balance sync: {$updated} updated, {$skipped} already synced, {$notFound} not found.");

        // Transfer to master accounts and set legacy_uber_balance = 0
        if (empty($accountsToTransfer)) {
            $this->info('No new ubers to transfer to master accounts.');
        } else {
            $this->newLine();
            $this->info('Transferring ubers to master accounts...');

            $transferred = 0;
            $totalTransferred = 0;

            foreach ($accountsToTransfer as $data) {
                $gameAccount = $data['gameAccount'];
                $totalUbers = $data['totalUbers'];
                $user = $gameAccount->user;

                if ($user) {
                    $user->increment('uber_balance', $totalUbers);
                    $gameAccount->update(['legacy_uber_balance' => 0]); // Set to 0 after transfer
                    $totalTransferred += $totalUbers;
                    $transferred++;

                    $this->output->write("\r  Transferred: {$transferred} accounts | Total ubers: {$totalTransferred}");
                }
            }

            $this->newLine();
            $this->info("Transfer complete: {$transferred} accounts, {$totalTransferred} ubers transferred.");
        }

        // Show remaining pending (unclaimed accounts)
        $pendingCount = GameAccount::where('server', 'xileretro')
            ->where('legacy_uber_balance', '>', 0)
            ->whereNull('user_id')
            ->count();
        $pendingSum = GameAccount::where('server', 'xileretro')
            ->where('legacy_uber_balance', '>', 0)
            ->whereNull('user_id')
            ->sum('legacy_uber_balance');

        if ($pendingCount > 0) {
            $this->info("Pending (unclaimed accounts): {$pendingCount} accounts with {$pendingSum} ubers awaiting claim.");
        }
    }

    protected function syncLegacyUberBalances(string $server, bool $dryRun, ?int $limit = null): int
    {
        if ($server !== 'xileretro') {
            $this->warn('Legacy uber sync is only available for XileRetro accounts.');

            return Command::SUCCESS;
        }

        $this->info('Fetching accounts with legacy ubers from XileRetro...');
        $this->newLine();

        // Query XileRetro_DonationUbers for accounts that have ubers
        $query = XileRetro_DonationUbers::where(function ($q) {
            $q->where('current_ubers', '>', 0)
                ->orWhere('pending_ubers', '>', 0);
        });

        if ($limit) {
            $query->limit($limit);
        }

        $legacyUberRecords = $query->get();

        if ($legacyUberRecords->isEmpty()) {
            $this->info('No accounts with legacy ubers found.');

            return Command::SUCCESS;
        }

        $this->info("Found {$legacyUberRecords->count()} accounts with legacy ubers.");
        $this->newLine();

        // Load all existing GameAccounts for this server upfront (keyed by ragnarok_account_id)
        $this->info('Loading existing game accounts...');
        $accountIds = $legacyUberRecords->pluck('account_id')->toArray();
        $existingGameAccounts = GameAccount::where('server', $server)
            ->whereIn('ragnarok_account_id', $accountIds)
            ->get()
            ->keyBy('ragnarok_account_id');

        $this->info("Found {$existingGameAccounts->count()} existing game accounts.");
        $this->newLine();

        $toUpdate = [];
        $toImport = [];

        foreach ($legacyUberRecords as $uberRecord) {
            $totalUbers = $uberRecord->total_ubers;

            // Check if GameAccount exists for this ragnarok account
            $gameAccount = $existingGameAccounts->get($uberRecord->account_id);

            if ($gameAccount) {
                // Skip accounts that already transferred ubers to master (has user + legacy_uber_balance = 0)
                // This prevents re-syncing accounts that have already been processed
                if ($gameAccount->user_id && $gameAccount->legacy_uber_balance == 0) {
                    continue;
                }

                // GameAccount exists - check if balance needs updating
                if ($totalUbers != $gameAccount->legacy_uber_balance) {
                    $toUpdate[] = [
                        'game_account' => $gameAccount,
                        'account_id' => $uberRecord->account_id,
                        'username' => $uberRecord->username,
                        'current_ubers' => $uberRecord->current_ubers,
                        'pending_ubers' => $uberRecord->pending_ubers,
                        'total_ubers' => $totalUbers,
                        'old_balance' => $gameAccount->legacy_uber_balance,
                        'has_user' => $gameAccount->user_id ? 'Yes' : 'No',
                    ];
                }
            } else {
                // GameAccount doesn't exist - needs to be imported
                $toImport[] = [
                    'account_id' => $uberRecord->account_id,
                    'username' => $uberRecord->username,
                    'current_ubers' => $uberRecord->current_ubers,
                    'pending_ubers' => $uberRecord->pending_ubers,
                    'total_ubers' => $totalUbers,
                ];
            }
        }

        // Display accounts to update
        if (! empty($toUpdate)) {
            $this->info('Accounts with uber balance changes:');
            $this->table(
                ['Account ID', 'Username', 'Current', 'Pending', 'Total', 'Old Balance', 'Has Master'],
                collect($toUpdate)->map(fn ($item) => [
                    $item['account_id'],
                    $item['username'],
                    $item['current_ubers'],
                    $item['pending_ubers'],
                    $item['total_ubers'],
                    $item['old_balance'],
                    $item['has_user'],
                ])->toArray()
            );
            $this->newLine();
        }

        // Display accounts to import
        if (! empty($toImport)) {
            $this->info('Accounts needing import (not yet in GameAccount):');
            $this->table(
                ['Account ID', 'Username', 'Current', 'Pending', 'Total'],
                collect($toImport)->map(fn ($item) => [
                    $item['account_id'],
                    $item['username'],
                    $item['current_ubers'],
                    $item['pending_ubers'],
                    $item['total_ubers'],
                ])->toArray()
            );
            $this->newLine();
        }

        if (empty($toUpdate) && empty($toImport)) {
            $this->info('All legacy uber balances are already in sync.');

            return Command::SUCCESS;
        }

        if ($dryRun) {
            $this->info('[DRY RUN] Would update '.count($toUpdate).' game accounts.');
            $this->info('[DRY RUN] Would import '.count($toImport).' new accounts.');

            return Command::SUCCESS;
        }

        $updated = 0;
        $imported = 0;
        $transferred = 0;
        $totalUbersTransferred = 0;

        // Update existing GameAccounts (auto-confirm if non-interactive)
        $shouldUpdate = ! empty($toUpdate) && (! $this->input->isInteractive() || $this->confirm('Update '.count($toUpdate).' existing game account uber balances?'));
        if ($shouldUpdate) {
            $this->withProgressBar($toUpdate, function ($item) use (&$updated, &$transferred, &$totalUbersTransferred) {
                $gameAccount = $item['game_account'];
                $totalUbers = $item['total_ubers'];

                DB::transaction(function () use ($gameAccount, $totalUbers, &$updated, &$transferred, &$totalUbersTransferred) {
                    // If account has a linked user, transfer ubers to user immediately
                    if ($gameAccount->user_id && $totalUbers > 0) {
                        $user = $gameAccount->user;
                        if ($user) {
                            $ubersToTransfer = $totalUbers - $gameAccount->legacy_uber_balance;
                            if ($ubersToTransfer > 0) {
                                $user->increment('uber_balance', $ubersToTransfer);
                                $totalUbersTransferred += $ubersToTransfer;
                                $transferred++;
                            }
                            // Set to 0 after transfer - prevents duplication if account is unlinked later
                            // The skip logic above prevents re-syncing already-transferred accounts
                            $gameAccount->update(['legacy_uber_balance' => 0]);
                        }
                    } else {
                        // No master account - store in legacy_uber_balance until claimed
                        $gameAccount->update(['legacy_uber_balance' => $totalUbers]);
                    }
                    $updated++;
                });
            });
            $this->newLine(2);
        }

        // Import new accounts (auto-confirm if non-interactive)
        $shouldImport = ! empty($toImport) && (! $this->input->isInteractive() || $this->confirm('Import '.count($toImport).' new accounts with legacy ubers?'));
        if ($shouldImport) {
            $loginClass = XileRetro_Login::class;

            $this->withProgressBar($toImport, function ($item) use ($loginClass, $server, &$imported) {
                $login = $loginClass::find($item['account_id']);
                if ($login && ! $this->isSystemEmail($login->email)) {
                    try {
                        $this->createAccountsFromLogin($login, $server);
                        $imported++;
                    } catch (Exception $e) {
                        // Skip failed imports
                    }
                }
            });
            $this->newLine(2);
        }

        $this->info("Sync complete: {$updated} updated, {$imported} imported.");

        if ($transferred > 0) {
            $this->info("Transferred {$totalUbersTransferred} ubers to {$transferred} master accounts.");
        }

        return Command::SUCCESS;
    }

    protected function interactiveImport(string $loginClass, string $server, bool $dryRun): int
    {
        // Query Login table directly from game database
        $logins = $loginClass::where('group_id', '<', 99)
            ->whereNotIn('email', $this->systemEmails)
            ->limit(20)
            ->get();

        if ($logins->isEmpty()) {
            $this->info('No accounts found in game database.');

            return Command::SUCCESS;
        }

        $this->info('Game accounts (showing first 20):');
        $this->newLine();

        if ($server === 'xileretro') {
            $tableData = $logins->map(fn ($login) => [
                $login->account_id,
                $login->userid,
                $login->email,
                $login->sex,
                $this->isValidEmail($login->email) ? 'Yes' : 'No',
                XileRetro_DonationUbers::getTotalUbersForAccount($login->account_id),
            ])->toArray();

            $this->table(['Account ID', 'Username', 'Email', 'Sex', 'Master Account?', 'Legacy Ubers'], $tableData);
        } else {
            $tableData = $logins->map(fn ($login) => [
                $login->account_id,
                $login->userid,
                $login->email,
                $login->sex,
                $this->isValidEmail($login->email) ? 'Yes' : 'No',
            ])->toArray();

            $this->table(['Account ID', 'Username', 'Email', 'Sex', 'Master Account?'], $tableData);
        }

        $this->newLine();
        $this->info('Use --account-id=<ID> to import a specific account');
        $this->info('Use --all to import all accounts (existing will be skipped)');

        return Command::SUCCESS;
    }

    protected function createAccountsFromLogin(XileRO_Login|XileRetro_Login $login, string $server): array
    {
        return DB::transaction(function () use ($login, $server) {
            // Check if already exists by ragnarok_account_id OR userid (skip duplicates)
            $existing = GameAccount::where('server', $server)
                ->where(function ($q) use ($login) {
                    $q->where('ragnarok_account_id', $login->account_id)
                        ->orWhere('userid', $login->userid);
                })
                ->first();

            if ($existing) {
                return [
                    'user' => $existing->user,
                    'gameAccount' => $existing,
                    'legacyUbers' => 0,
                    'wasCreated' => false,
                ];
            }

            $user = null;

            // Only create/link User if email is valid
            if ($this->isValidEmail($login->email)) {
                // Check if user with this email already exists
                $user = User::where('email', $login->email)->first();

                if (! $user) {
                    // Create new master account
                    $user = User::create([
                        'name' => $login->userid,
                        'email' => $login->email,
                        'password' => Hash::make(Str::random(32)), // Random password - user must reset
                    ]);
                }
            }

            // Fetch legacy ubers for XileRetro accounts
            $legacyUbers = 0;
            if ($server === 'xileretro') {
                $legacyUbers = XileRetro_DonationUbers::getTotalUbersForAccount($login->account_id);
            }

            // Create game account entry (links to existing login record)
            // user_id is nullable for accounts with fake emails (can be claimed later)
            $gameAccount = GameAccount::create([
                'user_id' => $user?->id,
                'server' => $server,
                'ragnarok_account_id' => $login->account_id,
                'userid' => $login->userid,
                'user_pass' => $login->user_pass,
                'email' => $login->email,
                'sex' => $login->sex,
                'group_id' => $login->group_id,
                'state' => $login->state,
                'legacy_uber_balance' => $legacyUbers,
            ]);

            $wasCreated = true;

            // If user was created/linked and there are legacy ubers, transfer them immediately
            // Only do this for newly created accounts
            if ($wasCreated && $user && $legacyUbers > 0) {
                $user->increment('uber_balance', $legacyUbers);
                $gameAccount->update(['legacy_uber_balance' => 0]); // Set to 0 after transfer
            }

            return [
                'user' => $user,
                'gameAccount' => $gameAccount,
                'legacyUbers' => $legacyUbers,
                'wasCreated' => $wasCreated,
            ];
        });
    }

    protected function displayLoginInfo(XileRO_Login|XileRetro_Login $login, bool $hasValidEmail = true, string $server = 'xilero'): void
    {
        $rows = [
            ['Account ID', $login->account_id],
            ['Username', $login->userid],
            ['Email', $login->email],
            ['Sex', $login->sex],
            ['Group ID', $login->group_id],
            ['Will Create Master Account', $hasValidEmail ? 'Yes' : 'No (can be claimed later)'],
        ];

        // Show legacy ubers for XileRetro accounts
        if ($server === 'xileretro') {
            $legacyUbers = XileRetro_DonationUbers::getTotalUbersForAccount($login->account_id);
            if ($legacyUbers > 0) {
                $rows[] = ['Legacy Ubers', $legacyUbers];
            }
        }

        $this->table(['Field', 'Value'], $rows);
    }

    protected function displayResult(?User $user, GameAccount $gameAccount, int $legacyUbers = 0): void
    {
        $this->newLine();

        if ($user) {
            $rows = [
                ['Master Account Email', $user->email],
                ['Master Account Name', $user->name],
                ['Game Username', $gameAccount->userid],
                ['Server', $gameAccount->serverName()],
                ['Ragnarok Account ID', $gameAccount->ragnarok_account_id],
            ];

            if ($legacyUbers > 0) {
                $rows[] = ['Legacy Ubers Transferred', $legacyUbers];
                $rows[] = ['New Master Uber Balance', $user->fresh()->uber_balance];
            }

            $this->table(['Field', 'Value'], $rows);
        } else {
            $rows = [
                ['Master Account', 'None (unclaimed - can be linked via support)'],
                ['Game Username', $gameAccount->userid],
                ['Game Email', $gameAccount->email],
                ['Server', $gameAccount->serverName()],
                ['Ragnarok Account ID', $gameAccount->ragnarok_account_id],
            ];

            if ($legacyUbers > 0) {
                $rows[] = ['Legacy Ubers (pending transfer)', $legacyUbers];
            }

            $this->table(['Field', 'Value'], $rows);
        }
    }
}
