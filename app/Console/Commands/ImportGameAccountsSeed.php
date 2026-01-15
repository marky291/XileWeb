<?php

namespace App\Console\Commands;

use App\Models\GameAccount;
use App\Models\User;
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

        // Skip accounts with default email
        if ($login->email === 'a@a.com') {
            $this->error("Account {$login->userid} has default email (a@a.com) and cannot be imported.");

            return Command::FAILURE;
        }

        // Check if already imported
        $existing = GameAccount::where('ragnarok_account_id', $accountId)
            ->where('server', $server)
            ->first();

        if ($existing) {
            $this->warn("Account {$login->userid} (ID: {$accountId}) is already linked to user: {$existing->user->email}");

            return Command::SUCCESS;
        }

        if ($dryRun) {
            $this->info('[DRY RUN] Would import:');
            $this->displayLoginInfo($login);

            return Command::SUCCESS;
        }

        $result = $this->createAccountsFromLogin($login, $server);

        if ($result) {
            $this->info('Account imported successfully!');
            $this->displayResult($result['user'], $result['gameAccount']);

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
            ->where('email', '!=', 'a@a.com'); // Exclude default email accounts

        if ($limit) {
            $query->limit($limit);
        }

        $logins = $query->get();

        if ($logins->isEmpty()) {
            $this->info('No unlinked accounts found to import.');

            return Command::SUCCESS;
        }

        $limitInfo = $limit ? " (limited to {$limit})" : '';
        $this->info("Found {$logins->count()} accounts to import{$limitInfo}.");
        $this->newLine();

        if ($dryRun) {
            $this->info('[DRY RUN] Would import the following accounts:');
            $this->newLine();

            $tableData = $logins->map(fn ($login) => [
                $login->account_id,
                $login->userid,
                $login->email,
                $login->sex,
            ])->toArray();

            $this->table(['Account ID', 'Username', 'Email', 'Sex'], $tableData);

            return Command::SUCCESS;
        }

        if (! $this->confirm("Import {$logins->count()} accounts?")) {
            $this->info('Import cancelled.');

            return Command::SUCCESS;
        }

        $imported = 0;
        $failed = 0;

        $this->withProgressBar($logins, function ($login) use ($server, &$imported, &$failed) {
            try {
                $this->createAccountsFromLogin($login, $server);
                $imported++;
            } catch (\Exception $e) {
                $failed++;
            }
        });

        $this->newLine(2);
        $this->info("Import complete: {$imported} imported, {$failed} failed.");

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
            ->where('email', '!=', 'a@a.com') // Exclude default email accounts
            ->limit(20)
            ->get();

        if ($logins->isEmpty()) {
            $this->info('No unlinked accounts found to import.');

            return Command::SUCCESS;
        }

        $this->info('Unlinked accounts (showing first 20):');
        $this->newLine();

        $tableData = $logins->map(fn ($login) => [
            $login->account_id,
            $login->userid,
            $login->email,
            $login->sex,
        ])->toArray();

        $this->table(['Account ID', 'Username', 'Email', 'Sex'], $tableData);

        $this->newLine();
        $this->info('Use --account-id=<ID> to import a specific account');
        $this->info('Use --all to import all unlinked accounts');

        return Command::SUCCESS;
    }

    protected function createAccountsFromLogin(XileRO_Login|XileRetro_Login $login, string $server): array
    {
        return DB::transaction(function () use ($login, $server) {
            // Check if user with this email already exists
            $user = User::where('email', $login->email)->first();

            if (! $user) {
                // Create new master account
                $user = User::create([
                    'name' => $login->userid,
                    'email' => $login->email ?: $login->userid.'@xilero.net',
                    'password' => Hash::make(Str::random(32)), // Random password - user must reset
                ]);
            }

            // Create game account entry (links to existing login record)
            $gameAccount = GameAccount::create([
                'user_id' => $user->id,
                'server' => $server,
                'ragnarok_account_id' => $login->account_id,
                'userid' => $login->userid,
                'user_pass' => $login->user_pass,
                'email' => $login->email,
                'sex' => $login->sex,
                'group_id' => $login->group_id,
                'state' => $login->state,
            ]);

            return [
                'user' => $user,
                'gameAccount' => $gameAccount,
            ];
        });
    }

    protected function displayLoginInfo(XileRO_Login|XileRetro_Login $login): void
    {
        $this->table(
            ['Field', 'Value'],
            [
                ['Account ID', $login->account_id],
                ['Username', $login->userid],
                ['Email', $login->email],
                ['Sex', $login->sex],
                ['Group ID', $login->group_id],
            ]
        );
    }

    protected function displayResult(User $user, GameAccount $gameAccount): void
    {
        $this->newLine();
        $this->table(
            ['Field', 'Value'],
            [
                ['Master Account Email', $user->email],
                ['Master Account Name', $user->name],
                ['Game Username', $gameAccount->userid],
                ['Server', $gameAccount->serverName()],
                ['Ragnarok Account ID', $gameAccount->ragnarok_account_id],
            ]
        );
    }
}
