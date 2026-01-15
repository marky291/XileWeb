<?php

namespace Tests\Unit\Models;

use App\Models\GameAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_have_game_accounts(): void
    {
        $user = User::factory()->create();
        $gameAccount = GameAccount::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->gameAccounts->contains($gameAccount));
    }

    public function test_user_can_create_game_account_when_under_limit(): void
    {
        $user = User::factory()->create(['max_game_accounts' => 6]);

        $this->assertTrue($user->canCreateGameAccount());
    }

    public function test_user_cannot_create_game_account_when_at_limit(): void
    {
        $user = User::factory()->create(['max_game_accounts' => 2]);
        GameAccount::factory()->count(2)->create(['user_id' => $user->id]);

        $this->assertFalse($user->canCreateGameAccount());
    }

    public function test_user_password_is_hashed(): void
    {
        $user = User::factory()->create(['password' => 'plaintext']);

        $this->assertNotEquals('plaintext', $user->password);
    }
}
