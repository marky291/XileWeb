<?php

namespace Tests\Feature\Console;

use App\Models\GameAccount;
use App\Models\User;
use App\XileRetro\XileRetro_DonationUbers;
use App\XileRetro\XileRetro_Login;
use App\XileRO\XileRO_Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ImportGameAccountsSeedTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function fails_with_invalid_server_option(): void
    {
        $this->artisan('player:import', ['--server' => 'invalid'])
            ->expectsOutput('Invalid server. Use --server=xilero or --server=xileretro')
            ->assertExitCode(1);
    }

    #[Test]
    public function single_import_fails_when_account_not_found(): void
    {
        $this->artisan('player:import', [
            '--server' => 'xilero',
            '--account-id' => 999999,
        ])
            ->expectsOutput('Account ID 999999 not found.')
            ->assertExitCode(1);
    }

    #[Test]
    public function single_import_fails_when_account_has_system_email(): void
    {
        $login = XileRO_Login::factory()->create([
            'userid' => 'systemuser',
            'email' => 'athena@athena.com',
        ]);

        $this->artisan('player:import', [
            '--server' => 'xilero',
            '--account-id' => $login->account_id,
        ])
            ->expectsOutput("Account {$login->userid} has system email (athena@athena.com) and cannot be imported.")
            ->assertExitCode(1);
    }

    #[Test]
    public function single_import_shows_warning_when_already_linked(): void
    {
        $user = User::factory()->create();
        $login = XileRO_Login::factory()->create([
            'userid' => 'existinguser',
            'email' => 'existing@example.com',
        ]);

        // Create the linked game account
        GameAccount::factory()->create([
            'user_id' => $user->id,
            'server' => 'xilero',
            'ragnarok_account_id' => $login->account_id,
            'userid' => $login->userid,
        ]);

        $this->artisan('player:import', [
            '--server' => 'xilero',
            '--account-id' => $login->account_id,
        ])
            ->expectsOutputToContain("Account {$login->userid} (ID: {$login->account_id}) is already imported")
            ->assertExitCode(0);
    }

    #[Test]
    public function single_import_shows_warning_for_unclaimed_account(): void
    {
        $login = XileRO_Login::factory()->create([
            'userid' => 'unclaimeduser',
            'email' => 'a@a.com',
        ]);

        // Create unclaimed game account (no user_id)
        GameAccount::factory()->unclaimed()->create([
            'server' => 'xilero',
            'ragnarok_account_id' => $login->account_id,
            'userid' => $login->userid,
        ]);

        $this->artisan('player:import', [
            '--server' => 'xilero',
            '--account-id' => $login->account_id,
        ])
            ->expectsOutputToContain('is already imported (master account: unclaimed)')
            ->assertExitCode(0);
    }

    #[Test]
    public function single_import_dry_run_shows_what_would_be_imported(): void
    {
        $login = XileRO_Login::factory()->create([
            'userid' => 'newuser',
            'email' => 'newuser@example.com',
        ]);

        $this->artisan('player:import', [
            '--server' => 'xilero',
            '--account-id' => $login->account_id,
            '--dry-run' => true,
        ])
            ->expectsOutput('[DRY RUN] Would import:')
            ->assertExitCode(0);

        // Verify nothing was created
        $this->assertDatabaseMissing('users', ['email' => 'newuser@example.com']);
        $this->assertDatabaseMissing('game_accounts', ['ragnarok_account_id' => $login->account_id]);
    }

    #[Test]
    public function single_import_creates_new_user_and_game_account_for_valid_email(): void
    {
        $login = XileRO_Login::factory()->create([
            'userid' => 'brandnewuser',
            'email' => 'brandnew@example.com',
            'user_pass' => 'hashedpassword',
            'sex' => 'M',
            'group_id' => 0,
            'state' => 0,
        ]);

        $this->artisan('player:import', [
            '--server' => 'xilero',
            '--account-id' => $login->account_id,
        ])
            ->expectsOutput('Account imported successfully!')
            ->assertExitCode(0);

        // Verify user was created
        $this->assertDatabaseHas('users', [
            'name' => 'brandnewuser',
            'email' => 'brandnew@example.com',
        ]);

        // Verify game account was created and linked
        $this->assertDatabaseHas('game_accounts', [
            'server' => 'xilero',
            'ragnarok_account_id' => $login->account_id,
            'userid' => 'brandnewuser',
            'email' => 'brandnew@example.com',
        ]);

        // Verify relationship
        $user = User::where('email', 'brandnew@example.com')->first();
        $gameAccount = GameAccount::where('ragnarok_account_id', $login->account_id)->first();
        $this->assertEquals($user->id, $gameAccount->user_id);
    }

    #[Test]
    public function single_import_creates_game_account_only_for_fake_email(): void
    {
        $login = XileRO_Login::factory()->create([
            'userid' => 'fakeemailuser',
            'email' => 'a@a.com', // Default fake email
            'user_pass' => 'hashedpassword',
            'sex' => 'M',
            'group_id' => 0,
            'state' => 0,
        ]);

        $this->artisan('player:import', [
            '--server' => 'xilero',
            '--account-id' => $login->account_id,
        ])
            ->expectsOutput('Account imported successfully!')
            ->assertExitCode(0);

        // Verify NO user was created
        $this->assertDatabaseCount('users', 0);

        // Verify game account was created without user_id
        $this->assertDatabaseHas('game_accounts', [
            'server' => 'xilero',
            'ragnarok_account_id' => $login->account_id,
            'userid' => 'fakeemailuser',
            'user_id' => null,
        ]);
    }

    #[Test]
    public function single_import_creates_game_account_only_for_numeric_fake_email(): void
    {
        $login = XileRO_Login::factory()->create([
            'userid' => 'numericemailuser',
            'email' => '2000013@a.com', // Auto-generated pattern
            'user_pass' => 'hashedpassword',
        ]);

        $this->artisan('player:import', [
            '--server' => 'xilero',
            '--account-id' => $login->account_id,
        ])
            ->expectsOutput('Account imported successfully!')
            ->assertExitCode(0);

        // Verify NO user was created
        $this->assertDatabaseCount('users', 0);

        // Verify game account was created without user_id
        $gameAccount = GameAccount::where('ragnarok_account_id', $login->account_id)->first();
        $this->assertNotNull($gameAccount);
        $this->assertNull($gameAccount->user_id);
    }

    #[Test]
    public function single_import_links_to_existing_user_with_same_email(): void
    {
        $existingUser = User::factory()->create([
            'name' => 'Existing User',
            'email' => 'shared@example.com',
        ]);

        $login = XileRO_Login::factory()->create([
            'userid' => 'gameaccount',
            'email' => 'shared@example.com',
        ]);

        $this->artisan('player:import', [
            '--server' => 'xilero',
            '--account-id' => $login->account_id,
        ])
            ->expectsOutput('Account imported successfully!')
            ->assertExitCode(0);

        // Verify no new user was created
        $this->assertEquals(1, User::where('email', 'shared@example.com')->count());

        // Verify game account was linked to existing user
        $gameAccount = GameAccount::where('ragnarok_account_id', $login->account_id)->first();
        $this->assertEquals($existingUser->id, $gameAccount->user_id);
    }

    #[Test]
    public function import_all_shows_message_when_no_accounts_in_game_database(): void
    {
        // No accounts in game database at all
        $this->artisan('player:import', [
            '--server' => 'xilero',
            '--all' => true,
        ])
            ->expectsOutput('No accounts found in game database.')
            ->assertExitCode(0);
    }

    #[Test]
    public function import_all_excludes_admin_accounts(): void
    {
        // Create an admin account (group_id = 99)
        XileRO_Login::factory()->admin()->create([
            'userid' => 'adminuser',
            'email' => 'admin@example.com',
        ]);

        // Create a regular account
        XileRO_Login::factory()->create([
            'userid' => 'regularuser',
            'email' => 'regular@example.com',
            'group_id' => 0,
        ]);

        $this->artisan('player:import', [
            '--server' => 'xilero',
            '--all' => true,
            '--dry-run' => true,
        ])
            ->expectsOutputToContain('Found 1 accounts in game database')
            ->assertExitCode(0);
    }

    #[Test]
    public function import_all_excludes_system_emails(): void
    {
        // Create account with system email (athena@athena.com)
        XileRO_Login::factory()->create([
            'userid' => 'systemaccount',
            'email' => 'athena@athena.com',
            'group_id' => 0,
        ]);

        // Create account with real email
        XileRO_Login::factory()->create([
            'userid' => 'realemail',
            'email' => 'real@example.com',
            'group_id' => 0,
        ]);

        $this->artisan('player:import', [
            '--server' => 'xilero',
            '--all' => true,
            '--dry-run' => true,
        ])
            ->expectsOutputToContain('Found 1 accounts in game database')
            ->assertExitCode(0);
    }

    #[Test]
    public function import_all_includes_fake_emails_as_unclaimed(): void
    {
        // Create account with fake email
        XileRO_Login::factory()->create([
            'userid' => 'fakeemail',
            'email' => 'a@a.com',
            'group_id' => 0,
        ]);

        // Create account with real email
        XileRO_Login::factory()->create([
            'userid' => 'realemail',
            'email' => 'real@example.com',
            'group_id' => 0,
        ]);

        $this->artisan('player:import', [
            '--server' => 'xilero',
            '--all' => true,
            '--dry-run' => true,
        ])
            ->expectsOutputToContain('Found 2 accounts in game database')
            ->assertExitCode(0);
    }

    #[Test]
    public function import_all_dry_run_displays_master_account_column(): void
    {
        XileRO_Login::factory()->create([
            'userid' => 'validuser',
            'email' => 'valid@example.com',
            'group_id' => 0,
        ]);

        XileRO_Login::factory()->create([
            'userid' => 'fakeuser',
            'email' => 'a@a.com',
            'group_id' => 0,
        ]);

        $this->artisan('player:import', [
            '--server' => 'xilero',
            '--all' => true,
            '--dry-run' => true,
        ])
            ->expectsOutputToContain('[DRY RUN] Sample accounts from game database')
            ->assertExitCode(0);

        // Verify nothing was created
        $this->assertDatabaseCount('game_accounts', 0);
    }

    #[Test]
    public function import_all_respects_limit_option(): void
    {
        // Create 5 unlinked accounts
        XileRO_Login::factory()->count(5)->create([
            'group_id' => 0,
        ])->each(function ($login, $index) {
            $login->update(['email' => "limited{$index}@example.com"]);
        });

        $this->artisan('player:import', [
            '--server' => 'xilero',
            '--all' => true,
            '--dry-run' => true,
            '--limit' => 2,
        ])
            ->expectsOutputToContain('Found 5 accounts in game database (limited to 2)')
            ->assertExitCode(0);
    }

    #[Test]
    public function import_all_actually_imports_mixed_accounts_when_confirmed(): void
    {
        $validLogin = XileRO_Login::factory()->create([
            'userid' => 'validemailuser',
            'email' => 'valid@example.com',
            'group_id' => 0,
        ]);

        $fakeLogin = XileRO_Login::factory()->create([
            'userid' => 'fakeemailuser',
            'email' => 'a@a.com',
            'group_id' => 0,
        ]);

        $this->artisan('player:import', [
            '--server' => 'xilero',
            '--all' => true,
        ])
            ->expectsConfirmation('Import new accounts (processing newest first, stops after 500 consecutive already-synced)?', 'yes')
            ->expectsOutputToContain('Import complete: 2 imported (1 with master account, 1 unclaimed)')
            ->assertExitCode(0);

        // Verify valid email account has user
        $validGameAccount = GameAccount::where('userid', 'validemailuser')->first();
        $this->assertNotNull($validGameAccount->user_id);

        // Verify fake email account has no user
        $fakeGameAccount = GameAccount::where('userid', 'fakeemailuser')->first();
        $this->assertNull($fakeGameAccount->user_id);

        // Only 1 user should be created
        $this->assertDatabaseCount('users', 1);
    }

    #[Test]
    public function import_all_cancelled_when_not_confirmed(): void
    {
        XileRO_Login::factory()->create([
            'userid' => 'cancelleduser',
            'email' => 'cancelled@example.com',
            'group_id' => 0,
        ]);

        $this->artisan('player:import', [
            '--server' => 'xilero',
            '--all' => true,
        ])
            ->expectsConfirmation('Import new accounts (processing newest first, stops after 500 consecutive already-synced)?', 'no')
            ->expectsOutput('Import cancelled.')
            ->assertExitCode(0);

        // Verify nothing was created
        $this->assertDatabaseCount('game_accounts', 0);
        $this->assertDatabaseCount('users', 0);
    }

    #[Test]
    public function interactive_mode_shows_accounts_with_master_column(): void
    {
        XileRO_Login::factory()->create([
            'userid' => 'validuser',
            'email' => 'valid@example.com',
            'group_id' => 0,
        ]);

        XileRO_Login::factory()->create([
            'userid' => 'fakeuser',
            'email' => 'a@a.com',
            'group_id' => 0,
        ]);

        $this->artisan('player:import', [
            '--server' => 'xilero',
        ])
            ->expectsOutput('Game accounts (showing first 20):')
            ->expectsOutput('Use --account-id=<ID> to import a specific account')
            ->expectsOutput('Use --all to import all accounts (existing will be skipped)')
            ->assertExitCode(0);
    }

    #[Test]
    public function interactive_mode_shows_message_when_no_accounts(): void
    {
        $this->artisan('player:import', [
            '--server' => 'xilero',
        ])
            ->expectsOutput('No accounts found in game database.')
            ->assertExitCode(0);
    }

    #[Test]
    public function works_with_xileretro_server(): void
    {
        $login = XileRetro_Login::factory()->create([
            'userid' => 'retrouser',
            'email' => 'retro@example.com',
            'group_id' => 0,
        ]);

        $this->artisan('player:import', [
            '--server' => 'xileretro',
            '--account-id' => $login->account_id,
        ])
            ->expectsOutput('Account imported successfully!')
            ->assertExitCode(0);

        $this->assertDatabaseHas('game_accounts', [
            'server' => 'xileretro',
            'ragnarok_account_id' => $login->account_id,
            'userid' => 'retrouser',
        ]);
    }

    #[Test]
    public function same_email_links_to_same_user_across_servers(): void
    {
        // Create the same account ID on both servers (simulating production scenario)
        $xileroLogin = XileRO_Login::factory()->create([
            'userid' => 'multiserver',
            'email' => 'multi@example.com',
            'group_id' => 0,
        ]);

        // Import from xilero
        $this->artisan('player:import', [
            '--server' => 'xilero',
            '--account-id' => $xileroLogin->account_id,
        ])->assertExitCode(0);

        // Create another login for xileretro (in tests both use same table)
        $xileretroLogin = XileRetro_Login::factory()->create([
            'userid' => 'retroversion',
            'email' => 'multi@example.com', // Same email - should link to same user
            'group_id' => 0,
        ]);

        // Import from xileretro - should link to existing user
        $this->artisan('player:import', [
            '--server' => 'xileretro',
            '--account-id' => $xileretroLogin->account_id,
        ])->assertExitCode(0);

        // Verify one user with two game accounts
        $user = User::where('email', 'multi@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals(2, $user->gameAccounts()->count());
        $this->assertTrue($user->gameAccounts()->where('server', 'xilero')->exists());
        $this->assertTrue($user->gameAccounts()->where('server', 'xileretro')->exists());
    }

    #[Test]
    public function import_preserves_all_login_fields(): void
    {
        $login = XileRO_Login::factory()->create([
            'userid' => 'detaileduser',
            'user_pass' => 'securepassword123',
            'email' => 'detailed@example.com',
            'sex' => 'F',
            'group_id' => 5,
            'state' => 1,
        ]);

        $this->artisan('player:import', [
            '--server' => 'xilero',
            '--account-id' => $login->account_id,
        ])->assertExitCode(0);

        $this->assertDatabaseHas('game_accounts', [
            'ragnarok_account_id' => $login->account_id,
            'userid' => 'detaileduser',
            'user_pass' => 'securepassword123',
            'email' => 'detailed@example.com',
            'sex' => 'F',
            'group_id' => 5,
            'state' => 1,
        ]);
    }

    #[Test]
    public function email_validation_detects_various_fake_patterns(): void
    {
        // Test various fake email patterns
        $fakeEmails = [
            'a@a.com',           // Default
            '2000013@a.com',     // Numeric@a.com
            '12345@xilero.net',  // Numeric@xilero.net
            '',                  // Empty
        ];

        foreach ($fakeEmails as $fakeEmail) {
            $login = XileRO_Login::factory()->create([
                'email' => $fakeEmail,
                'group_id' => 0,
            ]);

            $this->artisan('player:import', [
                '--server' => 'xilero',
                '--account-id' => $login->account_id,
            ])->assertExitCode(0);

            $gameAccount = GameAccount::where('ragnarok_account_id', $login->account_id)->first();
            $this->assertNull($gameAccount->user_id, "Email '{$fakeEmail}' should result in unclaimed account");
        }

        // No users should have been created
        $this->assertDatabaseCount('users', 0);
    }

    #[Test]
    public function email_validation_accepts_valid_emails(): void
    {
        $validEmails = [
            'user@example.com',
            'test.user@domain.org',
            'my-email@company.co.uk',
        ];

        foreach ($validEmails as $validEmail) {
            $login = XileRO_Login::factory()->create([
                'email' => $validEmail,
                'group_id' => 0,
            ]);

            $this->artisan('player:import', [
                '--server' => 'xilero',
                '--account-id' => $login->account_id,
            ])->assertExitCode(0);

            $gameAccount = GameAccount::where('ragnarok_account_id', $login->account_id)->first();
            $this->assertNotNull($gameAccount->user_id, "Email '{$validEmail}' should result in linked account");
        }

        // All 3 users should have been created
        $this->assertDatabaseCount('users', 3);
    }

    #[Test]
    public function xileretro_import_fetches_legacy_ubers_and_transfers_to_master(): void
    {
        $login = XileRetro_Login::factory()->create([
            'userid' => 'uberwallet',
            'email' => 'uberwallet@example.com',
            'group_id' => 0,
        ]);

        // Create legacy ubers record
        XileRetro_DonationUbers::factory()->create([
            'account_id' => $login->account_id,
            'username' => $login->userid,
            'current_ubers' => 50,
            'pending_ubers' => 25,
        ]);

        $this->artisan('player:import', [
            '--server' => 'xileretro',
            '--account-id' => $login->account_id,
        ])
            ->expectsOutput('Account imported successfully!')
            ->assertExitCode(0);

        // Verify user was created with uber balance transferred
        $user = User::where('email', 'uberwallet@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals(75, $user->uber_balance);

        // Verify game account was created with legacy_uber_balance cleared
        $gameAccount = GameAccount::where('ragnarok_account_id', $login->account_id)->first();
        $this->assertEquals(0, $gameAccount->legacy_uber_balance);
    }

    #[Test]
    public function xileretro_import_stores_legacy_ubers_when_no_master_account(): void
    {
        $login = XileRetro_Login::factory()->create([
            'userid' => 'legacyfake',
            'email' => 'a@a.com', // Fake email - no master account
            'group_id' => 0,
        ]);

        // Create legacy ubers record
        XileRetro_DonationUbers::factory()->create([
            'account_id' => $login->account_id,
            'username' => $login->userid,
            'current_ubers' => 100,
            'pending_ubers' => 50,
        ]);

        $this->artisan('player:import', [
            '--server' => 'xileretro',
            '--account-id' => $login->account_id,
        ])
            ->expectsOutput('Account imported successfully!')
            ->assertExitCode(0);

        // Verify no user was created
        $this->assertDatabaseCount('users', 0);

        // Verify game account was created with legacy_uber_balance stored (pending transfer)
        $gameAccount = GameAccount::where('ragnarok_account_id', $login->account_id)->first();
        $this->assertEquals(150, $gameAccount->legacy_uber_balance);
        $this->assertNull($gameAccount->user_id);
    }

    #[Test]
    public function xileretro_import_handles_account_without_legacy_ubers(): void
    {
        $login = XileRetro_Login::factory()->create([
            'userid' => 'noubers',
            'email' => 'noubers@example.com',
            'group_id' => 0,
        ]);

        // No legacy ubers record exists

        $this->artisan('player:import', [
            '--server' => 'xileretro',
            '--account-id' => $login->account_id,
        ])
            ->expectsOutput('Account imported successfully!')
            ->assertExitCode(0);

        // Verify user was created with 0 uber balance
        $user = User::where('email', 'noubers@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals(0, $user->uber_balance);

        // Verify game account was created with 0 legacy_uber_balance
        $gameAccount = GameAccount::where('ragnarok_account_id', $login->account_id)->first();
        $this->assertEquals(0, $gameAccount->legacy_uber_balance);
    }

    #[Test]
    public function xileretro_import_all_reports_legacy_ubers_transferred(): void
    {
        // Create accounts with and without legacy ubers
        $loginWithUbers = XileRetro_Login::factory()->create([
            'userid' => 'hasubers',
            'email' => 'hasubers@example.com',
            'group_id' => 0,
        ]);

        XileRetro_DonationUbers::factory()->create([
            'account_id' => $loginWithUbers->account_id,
            'username' => $loginWithUbers->userid,
            'current_ubers' => 100,
            'pending_ubers' => 0,
        ]);

        $loginWithoutUbers = XileRetro_Login::factory()->create([
            'userid' => 'noubers2',
            'email' => 'noubers2@example.com',
            'group_id' => 0,
        ]);

        $this->artisan('player:import', [
            '--server' => 'xileretro',
            '--all' => true,
        ])
            ->expectsConfirmation('Import new accounts (processing newest first, stops after 500 consecutive already-synced)?', 'yes')
            ->expectsOutputToContain('Import complete: 2 imported (2 with master account, 0 unclaimed)')
            ->expectsOutputToContain('Legacy ubers: 100 transferred to master accounts')
            ->assertExitCode(0);
    }

    #[Test]
    public function xileretro_import_all_dry_run_shows_legacy_ubers_column(): void
    {
        $login = XileRetro_Login::factory()->create([
            'userid' => 'dryrunubers',
            'email' => 'dryrun@example.com',
            'group_id' => 0,
        ]);

        XileRetro_DonationUbers::factory()->create([
            'account_id' => $login->account_id,
            'username' => $login->userid,
            'current_ubers' => 75,
            'pending_ubers' => 25,
        ]);

        $this->artisan('player:import', [
            '--server' => 'xileretro',
            '--all' => true,
            '--dry-run' => true,
        ])
            ->expectsOutputToContain('[DRY RUN] Sample accounts from game database')
            ->assertExitCode(0);

        // Nothing should be created
        $this->assertDatabaseCount('game_accounts', 0);
        $this->assertDatabaseCount('users', 0);
    }

    #[Test]
    public function xilero_import_does_not_set_legacy_ubers(): void
    {
        $login = XileRO_Login::factory()->create([
            'userid' => 'xilerouser',
            'email' => 'xilero@example.com',
            'group_id' => 0,
        ]);

        $this->artisan('player:import', [
            '--server' => 'xilero',
            '--account-id' => $login->account_id,
        ])->assertExitCode(0);

        // Verify game account has 0 legacy_uber_balance (XileRO doesn't have legacy ubers)
        $gameAccount = GameAccount::where('ragnarok_account_id', $login->account_id)->first();
        $this->assertEquals(0, $gameAccount->legacy_uber_balance);
    }
}
