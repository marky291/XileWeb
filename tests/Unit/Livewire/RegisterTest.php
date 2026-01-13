<?php

namespace Tests\Unit\Livewire;

use App\Livewire\Auth\GameAccountRegister;
use App\Ragnarok\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_account_is_created(): void
    {
        $component = Livewire::test(GameAccountRegister::class);

        $username = 'testUser';
        $email = 'test@email.com';
        $password = 'password123';

        $component->set('username', $username)
            ->set('email', $email)
            ->set('password', $password)
            ->set('password_confirmation', $password)
            ->call('register');

        $login = Login::where('email', $email)->first();

        $this->assertNotNull($login);
        $this->assertEquals($username, $login->userid);
        $this->assertEquals($email, $login->email);
        $this->assertAuthenticatedAs($login);
    }

    public function test_registration_validates_required_fields(): void
    {
        Livewire::test(GameAccountRegister::class)
            ->set('username', '')
            ->set('email', '')
            ->set('password', '')
            ->call('register')
            ->assertHasErrors(['username', 'email', 'password']);
    }

    public function test_username_is_alphanumeric(): void
    {
        Livewire::test(GameAccountRegister::class)
            ->set('username', 'test user!')
            ->call('register')
            ->assertHasErrors(['username']);
    }

    public function test_password_must_be_confirmed(): void
    {
        Livewire::test(GameAccountRegister::class)
            ->set('username', 'testuser')
            ->set('email', 'test@email.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'different')
            ->call('register')
            ->assertHasErrors(['password']);
    }
}
