<?php

namespace App\Console\Commands;

use App\Models\GameAccount;
use App\Models\User;
use App\XileRetro\XileRetro_DonationUbers;
use App\XileRetro\XileRetro_Login;
use App\XileRO\XileRO_Login;
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
                            {--limit= : Limit total number of accounts to import}
                            {--dry-run : Show what would be imported without making changes}';

    protected $description = 'Import game accounts from XileRO_Login/XileRetro_Login and create master accounts';

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
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        $dryRun = $this->option('dry-run');

        if (! in_array($server, ['xilero', 'xileretro'])) {
            $this->error('Invalid server. Use --server=xilero or --server=xileretro');

            return Command::FAILURE;
        }

        $loginClass = $server === 'xileretro' ? XileRetro_Login::class : XileRO_Login::class;
        $serverName = $server === 'xileretro' ? 'XileRetro' : 'XileRO';

        $this->info("Importing from {$serverName} ({$loginClass})...");
        $this->newLine();

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
        // Get all login records that are not yet linked
        $existingIds = GameAccount::where('server', $server)
            ->whereNotNull('ragnarok_account_id')
            ->pluck('ragnarok_account_id')
            ->toArray();

        $query = $loginClass::whereNotIn('account_id', $existingIds)
            ->where('group_id', '<', 99) // Exclude admin accounts
            ->whereNotIn('email', $this->systemEmails); // Exclude system accounts

        if ($limit) {
            $query->limit($limit);
        }

        $logins = $query->get();

        if ($logins->isEmpty()) {
            $this->info('No unlinked accounts found to import.');

            return Command::SUCCESS;
        }

        // Count accounts with valid vs fake emails
        $withValidEmail = $logins->filter(fn ($login) => $this->isValidEmail($login->email))->count();
        $withFakeEmail = $logins->count() - $withValidEmail;

        $limitInfo = $limit ? " (limited to {$limit})" : '';
        $this->info("Found {$logins->count()} accounts to import{$limitInfo}.");
        $this->info("  - {$withValidEmail} with valid email (will create master account)");
        $this->info("  - {$withFakeEmail} with fake/invalid email (game account only, can be claimed later)");
        $this->newLine();

        if ($dryRun) {
            $this->info('[DRY RUN] Would import the following accounts:');
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

            return Command::SUCCESS;
        }

        if (! $this->confirm("Import {$logins->count()} accounts?")) {
            $this->info('Import cancelled.');

            return Command::SUCCESS;
        }

        $imported = 0;
        $withMaster = 0;
        $withoutMaster = 0;
        $failed = 0;
        $totalLegacyUbers = 0;
        $pendingLegacyUbers = 0;

        $this->withProgressBar($logins, function ($login) use ($server, &$imported, &$withMaster, &$withoutMaster, &$failed, &$totalLegacyUbers, &$pendingLegacyUbers) {
            try {
                $result = $this->createAccountsFromLogin($login, $server);
                $imported++;
                $legacyUbers = $result['legacyUbers'] ?? 0;

                if ($result['user']) {
                    $withMaster++;
                    $totalLegacyUbers += $legacyUbers;
                } else {
                    $withoutMaster++;
                    $pendingLegacyUbers += $legacyUbers;
                }
            } catch (\Exception $e) {
                $failed++;
            }
        });

        $this->newLine(2);
        $this->info("Import complete: {$imported} imported ({$withMaster} with master account, {$withoutMaster} unclaimed), {$failed} failed.");

        if ($server === 'xileretro' && ($totalLegacyUbers > 0 || $pendingLegacyUbers > 0)) {
            $this->info("Legacy ubers: {$totalLegacyUbers} transferred to master accounts, {$pendingLegacyUbers} pending (on unclaimed accounts).");
        }

        return Command::SUCCESS;
    }

    protected function interactiveImport(string $loginClass, string $server, bool $dryRun): int
    {
        // Get unlinked accounts
        $existingIds = GameAccount::where('server', $server)
            ->whereNotNull('ragnarok_account_id')
            ->pluck('ragnarok_account_id')
            ->toArray();

        $logins = $loginClass::whereNotIn('account_id', $existingIds)
            ->where('group_id', '<', 99)
            ->whereNotIn('email', $this->systemEmails)
            ->limit(20)
            ->get();

        if ($logins->isEmpty()) {
            $this->info('No unlinked accounts found to import.');

            return Command::SUCCESS;
        }

        $this->info('Unlinked accounts (showing first 20):');
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
        $this->info('Use --all to import all unlinked accounts');

        return Command::SUCCESS;
    }

    protected function createAccountsFromLogin(XileRO_Login|XileRetro_Login $login, string $server): array
    {
        return DB::transaction(function () use ($login, $server) {
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

            // If user was created/linked and there are legacy ubers, transfer them immediately
            if ($user && $legacyUbers > 0) {
                $user->increment('uber_balance', $legacyUbers);
                $gameAccount->update(['legacy_uber_balance' => 0]);
            }

            return [
                'user' => $user,
                'gameAccount' => $gameAccount,
                'legacyUbers' => $legacyUbers,
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
