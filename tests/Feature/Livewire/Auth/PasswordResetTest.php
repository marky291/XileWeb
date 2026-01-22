<?php

namespace Tests\Feature\Livewire\Auth;

use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\ResetPassword;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_page_can_be_rendered(): void
    {
        $response = $this->get(route('password.request'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(ForgotPassword::class);
    }

    public function test_reset_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        Livewire::test(ForgotPassword::class)
            ->set('email', $user->email)
            ->call('sendResetLink')
            ->assertSet('emailSent', true);

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_reset_link_request_validates_email(): void
    {
        Livewire::test(ForgotPassword::class)
            ->set('email', 'not-an-email')
            ->call('sendResetLink')
            ->assertHasErrors(['email']);
    }

    public function test_reset_password_page_can_be_rendered(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->get(route('password.reset', ['token' => $token, 'email' => $user->email]));

        $response->assertStatus(200);
        $response->assertSeeLivewire(ResetPassword::class);
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        Livewire::test(ResetPassword::class, ['token' => $token])
            ->set('email', $user->email)
            ->set('password', 'newpassword123')
            ->set('password_confirmation', 'newpassword123')
            ->call('resetPassword')
            ->assertRedirect(route('dashboard'));

        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
        $this->assertAuthenticatedAs($user);
    }

    public function test_password_reset_validates_password_confirmation(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        Livewire::test(ResetPassword::class, ['token' => $token])
            ->set('email', $user->email)
            ->set('password', 'newpassword123')
            ->set('password_confirmation', 'different')
            ->call('resetPassword')
            ->assertHasErrors(['password']);
    }

    public function test_password_reset_fails_with_invalid_token(): void
    {
        $user = User::factory()->create();

        Livewire::test(ResetPassword::class, ['token' => 'invalid-token'])
            ->set('email', $user->email)
            ->set('password', 'newpassword123')
            ->set('password_confirmation', 'newpassword123')
            ->call('resetPassword')
            ->assertHasErrors(['email']);
    }
}
