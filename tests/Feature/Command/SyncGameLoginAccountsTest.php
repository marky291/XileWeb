<?php

namespace Tests\Feature\Command;

use App\Console\Commands\SyncGameLoginAccounts;
use App\Models\User;
use App\Ragnarok\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SyncGameLoginAccountsTest extends TestCase
{
    use RefreshDatabase;


    public function test_sync_login_accounts(): void
    {
        Login::create([
            'userid' => 'test',
            'email' => 'test@test.com'
        ]);

        $command = new SyncGameLoginAccounts();

        $command->handle();

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', ['name' => 'test', 'email' => 'test@test.com']);
    }

    public function test_sync_login_accounts_duplicate_account(): void
    {
        User::create([
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => Hash::make('test')
        ]);

        Login::create([
            'userid' => 'test',
            'email' => 'test@test.com'
        ]);

        $command = new SyncGameLoginAccounts();

        $command->handle();

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', ['name' => 'test', 'email' => 'test@test.com']);
    }

    public function test_sync_login_accounts_duplicate_emails(): void
    {
        Login::create([
            'userid' => 'test1',
            'email' => 'a@a.com'
        ]);

        Login::create([
            'userid' => 'test2',
            'email' => 'a@a.com'
        ]);

        $command = new SyncGameLoginAccounts();

        $command->handle();

        $this->assertDatabaseCount('users', 2);
    }

    public function test_sync_login_accounts_alternate_duplicate_emails(): void
    {
        Login::create([
            'userid' => 'test1',
            'email' => 'test@test.com'
        ]);

        Login::create([
            'userid' => 'test2',
            'email' => 'test@test.com'
        ]);

        $command = new SyncGameLoginAccounts();

        $command->handle();

        $this->assertDatabaseCount('users', 2);
    }

    public function test_sync_login_accounts_sync_group_id(): void
    {
        Login::create([
            'userid' => 'test1',
            'email' => 'test@test.com',
            'group_id' => 0
        ]);

        Login::create([
            'userid' => 'test2',
            'email' => 'test@test.com',
            'group_id' => 99
        ]);

        $command = new SyncGameLoginAccounts();

        $command->handle();

        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseHas('users', ['name' => 'test1', 'group_id' => 0]);
        $this->assertDatabaseHas('users', ['name' => 'test2', 'group_id' => 99]);
    }

    public function test_sync_user_logins(): void
    {
        Login::create([
            'account_id' => 1,
            'userid' => 'test',
            'email' => 'test@test.com'
        ]);

        Login::create([
            'account_id' => 2,
            'userid' => 'test',
            'email' => 'test@test.com'
        ]);

        $command = new SyncGameLoginAccounts();

        $command->handle();

        $this->assertDatabaseCount('user_logins', 2);
        $this->assertCount(2, User::first()->logins);
    }
}
