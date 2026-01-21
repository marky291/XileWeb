<?php

namespace Tests\Unit\Models;

use App\Models\GameAccount;
use App\Models\SyncedCharacter;
use App\Models\User;
use App\XileRO\XileRO_Char;
use App\XileRO\XileRO_Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GameAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_game_account_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $gameAccount = GameAccount::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($user->id, $gameAccount->user->id);
    }

    public function test_game_account_has_required_fields(): void
    {
        $user = User::factory()->create();
        $gameAccount = GameAccount::factory()->create([
            'user_id' => $user->id,
            'userid' => 'testaccount',
            'email' => 'game@test.com',
            'sex' => 'M',
        ]);

        $this->assertEquals('testaccount', $gameAccount->userid);
        $this->assertEquals('game@test.com', $gameAccount->email);
        $this->assertEquals('M', $gameAccount->sex);
    }

    public function test_game_account_password_is_hidden(): void
    {
        $gameAccount = GameAccount::factory()->create();

        $this->assertArrayNotHasKey('user_pass', $gameAccount->toArray());
    }

    #[Test]
    public function it_returns_server_name_for_xilero(): void
    {
        $gameAccount = GameAccount::factory()->create(['server' => 'xilero']);

        $this->assertEquals('XileRO (MidRate)', $gameAccount->serverName());
    }

    #[Test]
    public function it_returns_server_name_for_xileretro(): void
    {
        $gameAccount = GameAccount::factory()->create(['server' => 'xileretro']);

        $this->assertEquals('XileRetro (HighRate)', $gameAccount->serverName());
    }

    #[Test]
    public function it_returns_server_for_unknown_server(): void
    {
        $account = GameAccount::factory()->create(['server' => 'unknown']);

        $this->assertEquals('unknown', $account->serverName());
    }

    #[Test]
    public function it_returns_ragnarok_login_for_xilero(): void
    {
        $login = XileRO_Login::factory()->create();
        $gameAccount = GameAccount::factory()->create([
            'server' => 'xilero',
            'ragnarok_account_id' => $login->account_id,
        ]);

        $ragnarokLogin = $gameAccount->ragnarokLogin();

        $this->assertInstanceOf(XileRO_Login::class, $ragnarokLogin);
        $this->assertEquals($login->account_id, $ragnarokLogin->account_id);
    }

    #[Test]
    public function it_returns_null_when_no_ragnarok_account_id(): void
    {
        $gameAccount = GameAccount::factory()->create([
            'ragnarok_account_id' => null,
        ]);

        $this->assertNull($gameAccount->ragnarokLogin());
    }

    #[Test]
    public function it_has_synced_characters_relationship(): void
    {
        $gameAccount = GameAccount::factory()->create();

        SyncedCharacter::factory()->count(3)->create([
            'game_account_id' => $gameAccount->id,
        ]);

        $this->assertEquals(3, $gameAccount->syncedCharacters()->count());
    }

    #[Test]
    public function it_detects_online_characters(): void
    {
        $gameAccount = GameAccount::factory()->create([
            'server' => 'xilero',
            'ragnarok_account_id' => 12345,
        ]);

        // No characters - should be false
        $this->assertFalse($gameAccount->hasOnlineCharacters());

        // Create offline character
        XileRO_Char::factory()->create([
            'account_id' => 12345,
            'online' => 0,
        ]);

        $this->assertFalse($gameAccount->hasOnlineCharacters());

        // Create online character
        XileRO_Char::factory()->create([
            'account_id' => 12345,
            'online' => 1,
        ]);

        $this->assertTrue($gameAccount->hasOnlineCharacters());
    }

    #[Test]
    public function it_returns_false_for_online_check_without_ragnarok_id(): void
    {
        $gameAccount = GameAccount::factory()->create([
            'ragnarok_account_id' => null,
        ]);

        $this->assertFalse($gameAccount->hasOnlineCharacters());
    }

    #[Test]
    public function it_has_chars_relationship(): void
    {
        $gameAccount = GameAccount::factory()->create([
            'server' => 'xilero',
            'ragnarok_account_id' => 12345,
        ]);

        XileRO_Char::factory()->count(2)->create([
            'account_id' => 12345,
        ]);

        $this->assertEquals(2, $gameAccount->chars()->count());
    }

    #[Test]
    public function it_casts_has_security_code_to_boolean(): void
    {
        $account = GameAccount::factory()->create(['has_security_code' => 1]);
        $this->assertTrue($account->has_security_code);

        $account = GameAccount::factory()->create(['has_security_code' => 0]);
        $this->assertFalse($account->has_security_code);
    }

    #[Test]
    public function server_constants_are_correct(): void
    {
        $this->assertEquals('xilero', GameAccount::SERVER_XILERO);
        $this->assertEquals('xileretro', GameAccount::SERVER_XILERETRO);
    }

    #[Test]
    public function servers_array_contains_both_servers(): void
    {
        $this->assertArrayHasKey('xilero', GameAccount::SERVERS);
        $this->assertArrayHasKey('xileretro', GameAccount::SERVERS);
    }
}
