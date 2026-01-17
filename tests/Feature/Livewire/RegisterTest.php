<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Auth\Register;
use App\Models\User;
use App\Notifications\WelcomeNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_account_is_created(): void
    {
        $email = 'test@email.com';
        $password = 'password123';

        Livewire::test(Register::class)
            ->set('email', $email)
            ->set('password', $password)
            ->set('password_confirmation', $password)
            ->call('register');

        $user = User::where('email', $email)->first();

        $this->assertNotNull($user);
        $this->assertEquals($email, $user->email);
        $this->assertAuthenticatedAs($user);
    }

    public function test_registration_validates_required_fields(): void
    {
        Livewire::test(Register::class)
            ->set('email', '')
            ->set('password', '')
            ->call('register')
            ->assertHasErrors(['email', 'password']);
    }

    public function test_email_must_be_unique(): void
    {
        User::factory()->create(['email' => 'existing@email.com']);

        Livewire::test(Register::class)
            ->set('email', 'existing@email.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors(['email']);
    }

    public function test_password_must_be_confirmed(): void
    {
        Livewire::test(Register::class)
            ->set('email', 'test@email.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'different')
            ->call('register')
            ->assertHasErrors(['password']);
    }

    public function test_password_must_be_minimum_8_characters(): void
    {
        Livewire::test(Register::class)
            ->set('email', 'test@email.com')
            ->set('password', 'short')
            ->set('password_confirmation', 'short')
            ->call('register')
            ->assertHasErrors(['password']);
    }

    public function test_new_user_has_default_game_account_limit(): void
    {
        Livewire::test(Register::class)
            ->set('email', 'test@email.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register');

        $user = User::where('email', 'test@email.com')->first();
        $this->assertEquals(6, $user->max_game_accounts);
    }

    public function test_welcome_email_is_sent_on_registration(): void
    {
        Notification::fake();

        Livewire::test(Register::class)
            ->set('email', 'newuser@email.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register');

        $user = User::where('email', 'newuser@email.com')->first();

        Notification::assertSentTo($user, WelcomeNotification::class);
    }
}
