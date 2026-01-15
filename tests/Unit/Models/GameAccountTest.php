<?php

namespace Tests\Unit\Models;

use App\Models\GameAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
