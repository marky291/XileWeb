<?php

namespace Tests\Unit\Actions;

use App\Actions\CreateGameAccount;
use App\Models\GameAccount;
use App\Models\User;
use App\XileRetro\XileRetro_Login;
use App\XileRO\XileRO_Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateGameAccountTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_xilero_game_account(): void
    {
        $user = User::factory()->create();

        $data = [
            'server' => 'xilero',
            'userid' => 'testaccount',
            'email' => 'test@example.com',
            'password' => 'password123',
            'sex' => 'M',
        ];

        $gameAccount = CreateGameAccount::run($user, $data);

        $this->assertInstanceOf(GameAccount::class, $gameAccount);
        $this->assertEquals($user->id, $gameAccount->user_id);
        $this->assertEquals('xilero', $gameAccount->server);
        $this->assertEquals('testaccount', $gameAccount->userid);
        $this->assertEquals('test@example.com', $gameAccount->email);
        $this->assertNotNull($gameAccount->ragnarok_account_id);
    }

    #[Test]
    public function it_creates_xileretro_game_account(): void
    {
        $user = User::factory()->create();

        $data = [
            'server' => 'xileretro',
            'userid' => 'retroaccount',
            'email' => 'retro@example.com',
            'password' => 'password123',
            'sex' => 'F',
        ];

        $gameAccount = CreateGameAccount::run($user, $data);

        $this->assertInstanceOf(GameAccount::class, $gameAccount);
        $this->assertEquals('xileretro', $gameAccount->server);
        $this->assertEquals('retroaccount', $gameAccount->userid);
        $this->assertEquals('F', $gameAccount->sex);
    }

    #[Test]
    public function it_defaults_to_xilero_server(): void
    {
        $user = User::factory()->create();

        $data = [
            'userid' => 'defaultserver',
            'email' => 'default@example.com',
            'password' => 'password123',
        ];

        $gameAccount = CreateGameAccount::run($user, $data);

        $this->assertEquals('xilero', $gameAccount->server);
    }

    #[Test]
    public function it_defaults_sex_to_male(): void
    {
        $user = User::factory()->create();

        $data = [
            'server' => 'xilero',
            'userid' => 'nosex',
            'email' => 'nosex@example.com',
            'password' => 'password123',
        ];

        $gameAccount = CreateGameAccount::run($user, $data);

        $this->assertEquals('M', $gameAccount->sex);
    }

    #[Test]
    public function it_sets_default_group_id_to_zero(): void
    {
        $user = User::factory()->create();

        $data = [
            'server' => 'xilero',
            'userid' => 'grouptest',
            'email' => 'group@example.com',
            'password' => 'password123',
        ];

        $gameAccount = CreateGameAccount::run($user, $data);

        $this->assertEquals(0, $gameAccount->group_id);
    }

    #[Test]
    public function it_sets_default_state_to_zero(): void
    {
        $user = User::factory()->create();

        $data = [
            'server' => 'xilero',
            'userid' => 'statetest',
            'email' => 'state@example.com',
            'password' => 'password123',
        ];

        $gameAccount = CreateGameAccount::run($user, $data);

        $this->assertEquals(0, $gameAccount->state);
    }

    #[Test]
    public function it_hashes_password(): void
    {
        $user = User::factory()->create();

        $data = [
            'server' => 'xilero',
            'userid' => 'hashtest',
            'email' => 'hash@example.com',
            'password' => 'plaintext123',
        ];

        $gameAccount = CreateGameAccount::run($user, $data);

        // Password should be hashed (64 char SHA256)
        $this->assertEquals(64, strlen($gameAccount->user_pass));
        $this->assertNotEquals('plaintext123', $gameAccount->user_pass);
    }

    #[Test]
    public function it_creates_record_in_game_database(): void
    {
        $user = User::factory()->create();

        $data = [
            'server' => 'xilero',
            'userid' => 'gamedbtest',
            'email' => 'gamedb@example.com',
            'password' => 'password123',
        ];

        $gameAccount = CreateGameAccount::run($user, $data);

        // Should have a ragnarok_account_id from the game database
        $this->assertNotNull($gameAccount->ragnarok_account_id);

        // Should be able to find the record in game DB
        $login = XileRO_Login::find($gameAccount->ragnarok_account_id);
        $this->assertNotNull($login);
        $this->assertEquals('gamedbtest', $login->userid);
    }

    #[Test]
    public function it_returns_game_account_instance(): void
    {
        $user = User::factory()->create();

        $data = [
            'server' => 'xilero',
            'userid' => 'returntest',
            'email' => 'return@example.com',
            'password' => 'password123',
        ];

        $result = CreateGameAccount::run($user, $data);

        $this->assertInstanceOf(GameAccount::class, $result);
    }

    #[Test]
    public function it_links_game_account_to_user(): void
    {
        $user = User::factory()->create();

        $data = [
            'server' => 'xilero',
            'userid' => 'linktest',
            'email' => 'link@example.com',
            'password' => 'password123',
        ];

        CreateGameAccount::run($user, $data);

        $this->assertEquals(1, $user->gameAccounts()->count());
    }
}
