<?php

namespace Tests\Unit\Actions;

use App\Actions\MakeHashedLoginPassword;
use App\Actions\ResetGameAccountPassword;
use App\Models\GameAccount;
use App\Models\User;
use App\XileRO\XileRO_Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ResetGameAccountPasswordTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_password_in_local_database(): void
    {
        $user = User::factory()->create();
        $gameAccount = GameAccount::factory()->create([
            'user_id' => $user->id,
            'server' => 'xilero',
            'user_pass' => 'old_hashed_password',
        ]);

        $newPassword = 'newpassword123';

        ResetGameAccountPassword::run($gameAccount, $newPassword);

        $expectedHash = MakeHashedLoginPassword::run($newPassword, 'xilero');

        $this->assertEquals($expectedHash, $gameAccount->fresh()->user_pass);
    }

    #[Test]
    public function it_updates_password_in_game_database(): void
    {
        $user = User::factory()->create();

        // Create a login in game database first
        $login = XileRO_Login::factory()->create();

        $gameAccount = GameAccount::factory()->create([
            'user_id' => $user->id,
            'server' => 'xilero',
            'ragnarok_account_id' => $login->account_id,
            'user_pass' => 'old_hash',
        ]);

        $newPassword = 'newpassword456';

        ResetGameAccountPassword::run($gameAccount, $newPassword);

        $expectedHash = MakeHashedLoginPassword::run($newPassword, 'xilero');

        // Check game database was updated
        $this->assertEquals($expectedHash, $login->fresh()->user_pass);
    }

    #[Test]
    public function it_handles_missing_ragnarok_login(): void
    {
        $user = User::factory()->create();
        $gameAccount = GameAccount::factory()->create([
            'user_id' => $user->id,
            'server' => 'xilero',
            'ragnarok_account_id' => null, // No game DB link
            'user_pass' => 'old_hash',
        ]);

        $newPassword = 'newpassword789';

        // Should not throw exception
        ResetGameAccountPassword::run($gameAccount, $newPassword);

        $expectedHash = MakeHashedLoginPassword::run($newPassword, 'xilero');

        // Local database should still be updated
        $this->assertEquals($expectedHash, $gameAccount->fresh()->user_pass);
    }

    #[Test]
    public function it_uses_correct_server_for_hashing(): void
    {
        $user = User::factory()->create();

        $xileroAccount = GameAccount::factory()->create([
            'user_id' => $user->id,
            'server' => 'xilero',
            'user_pass' => 'old',
        ]);

        $xileretroAccount = GameAccount::factory()->create([
            'user_id' => $user->id,
            'server' => 'xileretro',
            'user_pass' => 'old',
        ]);

        $password = 'samepassword';

        ResetGameAccountPassword::run($xileroAccount, $password);
        ResetGameAccountPassword::run($xileretroAccount, $password);

        $xileroHash = MakeHashedLoginPassword::run($password, 'xilero');
        $xileretroHash = MakeHashedLoginPassword::run($password, 'xileretro');

        $this->assertEquals($xileroHash, $xileroAccount->fresh()->user_pass);
        $this->assertEquals($xileretroHash, $xileretroAccount->fresh()->user_pass);
    }
}
