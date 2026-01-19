<?php

namespace Tests\Feature;

use App\Actions\MakeHashedLoginPassword;
use App\Actions\ResetGameAccountPassword;
use App\Livewire\Auth\Dashboard;
use App\Models\GameAccount;
use App\Models\User;
use App\Notifications\GameAccountPasswordResetNotification;
use App\XileRO\XileRO_Char;
use App\XileRO\XileRO_Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class ResetGameAccountPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_reset_password_for_their_own_game_account(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $login = XileRO_Login::factory()->create([
            'userid' => 'testaccount',
            'user_pass' => MakeHashedLoginPassword::run('oldpassword'),
        ]);

        $gameAccount = GameAccount::factory()->create([
            'user_id' => $user->id,
            'server' => 'xilero',
            'ragnarok_account_id' => $login->account_id,
            'userid' => 'testaccount',
            'user_pass' => MakeHashedLoginPassword::run('oldpassword'),
        ]);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->call('showPasswordResetForm', $gameAccount->id)
            ->assertSet('resettingPasswordFor', $gameAccount->id)
            ->set('newPassword', 'newpassword123')
            ->set('newPassword_confirmation', 'newpassword123')
            ->call('resetPassword')
            ->assertSet('resettingPasswordFor', null)
            ->assertSee('Password has been reset for testaccount.');

        // Verify password was updated in both databases
        $expectedHash = MakeHashedLoginPassword::run('newpassword123');

        $this->assertEquals($expectedHash, $login->fresh()->user_pass);
        $this->assertEquals($expectedHash, $gameAccount->fresh()->user_pass);

        // Verify notification was sent
        Notification::assertSentTo($user, GameAccountPasswordResetNotification::class);
    }

    public function test_user_cannot_reset_password_for_account_they_dont_own(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $gameAccount = GameAccount::factory()->create([
            'user_id' => $otherUser->id,
            'server' => 'xilero',
        ]);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->call('showPasswordResetForm', $gameAccount->id)
            ->assertSee('Game account not found.')
            ->assertSet('resettingPasswordFor', null);
    }

    public function test_user_cannot_reset_password_while_characters_are_online(): void
    {
        $user = User::factory()->create();

        $login = XileRO_Login::factory()->create([
            'userid' => 'onlineaccount',
        ]);

        $gameAccount = GameAccount::factory()->create([
            'user_id' => $user->id,
            'server' => 'xilero',
            'ragnarok_account_id' => $login->account_id,
            'userid' => 'onlineaccount',
        ]);

        // Create an online character
        XileRO_Char::factory()->create([
            'account_id' => $login->account_id,
            'name' => 'OnlineChar',
            'online' => 1,
        ]);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->call('showPasswordResetForm', $gameAccount->id)
            ->assertSee('Cannot reset password while logged in. Please log out of the game first.')
            ->assertSet('resettingPasswordFor', null);
    }

    public function test_user_cannot_reset_password_if_character_goes_online_after_opening_modal(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $login = XileRO_Login::factory()->create([
            'userid' => 'testaccount2',
        ]);

        $gameAccount = GameAccount::factory()->create([
            'user_id' => $user->id,
            'server' => 'xilero',
            'ragnarok_account_id' => $login->account_id,
            'userid' => 'testaccount2',
        ]);

        $component = Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->call('showPasswordResetForm', $gameAccount->id)
            ->assertSet('resettingPasswordFor', $gameAccount->id);

        // Simulate character going online after modal was opened
        XileRO_Char::factory()->create([
            'account_id' => $login->account_id,
            'name' => 'NowOnlineChar',
            'online' => 1,
        ]);

        // Try to reset password
        $component
            ->set('newPassword', 'newpassword123')
            ->set('newPassword_confirmation', 'newpassword123')
            ->call('resetPassword')
            ->assertSee('Cannot reset password while logged in. Please log out of the game first.')
            ->assertSet('resettingPasswordFor', null);

        // Verify notification was NOT sent
        Notification::assertNotSentTo($user, GameAccountPasswordResetNotification::class);
    }

    public function test_password_validation_rules_are_enforced(): void
    {
        $user = User::factory()->create();

        $login = XileRO_Login::factory()->create();

        $gameAccount = GameAccount::factory()->create([
            'user_id' => $user->id,
            'server' => 'xilero',
            'ragnarok_account_id' => $login->account_id,
        ]);

        // Test password too short
        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->call('showPasswordResetForm', $gameAccount->id)
            ->set('newPassword', 'short')
            ->set('newPassword_confirmation', 'short')
            ->call('resetPassword')
            ->assertHasErrors(['newPassword' => 'min']);

        // Test password confirmation mismatch
        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->call('showPasswordResetForm', $gameAccount->id)
            ->set('newPassword', 'validpassword')
            ->set('newPassword_confirmation', 'differentpassword')
            ->call('resetPassword')
            ->assertHasErrors(['newPassword' => 'confirmed']);

        // Test password too long
        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->call('showPasswordResetForm', $gameAccount->id)
            ->set('newPassword', str_repeat('a', 32))
            ->set('newPassword_confirmation', str_repeat('a', 32))
            ->call('resetPassword')
            ->assertHasErrors(['newPassword' => 'max']);
    }

    public function test_cancel_password_reset_clears_form(): void
    {
        $user = User::factory()->create();

        $login = XileRO_Login::factory()->create();

        $gameAccount = GameAccount::factory()->create([
            'user_id' => $user->id,
            'server' => 'xilero',
            'ragnarok_account_id' => $login->account_id,
        ]);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->call('showPasswordResetForm', $gameAccount->id)
            ->assertSet('resettingPasswordFor', $gameAccount->id)
            ->set('newPassword', 'somepassword')
            ->set('newPassword_confirmation', 'somepassword')
            ->call('cancelPasswordReset')
            ->assertSet('resettingPasswordFor', null)
            ->assertSet('newPassword', '')
            ->assertSet('newPassword_confirmation', '');
    }

    public function test_reset_game_account_password_action(): void
    {
        $login = XileRO_Login::factory()->create([
            'user_pass' => MakeHashedLoginPassword::run('oldpassword'),
        ]);

        $gameAccount = GameAccount::factory()->create([
            'server' => 'xilero',
            'ragnarok_account_id' => $login->account_id,
            'user_pass' => MakeHashedLoginPassword::run('oldpassword'),
        ]);

        ResetGameAccountPassword::run($gameAccount, 'newpassword');

        $expectedHash = MakeHashedLoginPassword::run('newpassword');

        $this->assertEquals($expectedHash, $login->fresh()->user_pass);
        $this->assertEquals($expectedHash, $gameAccount->fresh()->user_pass);
    }

    public function test_notification_contains_correct_game_account_info(): void
    {
        $user = User::factory()->create();

        $gameAccount = GameAccount::factory()->create([
            'user_id' => $user->id,
            'server' => 'xilero',
            'userid' => 'myaccount',
        ]);

        $notification = new GameAccountPasswordResetNotification($gameAccount);
        $mail = $notification->toMail($user);

        $this->assertEquals('Game Account Password Changed', $mail->subject);
        $this->assertStringContainsString('myaccount', implode(' ', $mail->introLines));
        $this->assertStringContainsString('XileRO (MidRate)', implode(' ', $mail->introLines));
    }
}
