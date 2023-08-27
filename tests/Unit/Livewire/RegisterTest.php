<?php

namespace Tests\Unit\Livewire;

use App\Livewire\Register;
use App\Models\User;
use App\Ragnarok\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function testUserIsAttachedToLoginRecord()
    {
        // Create a Livewire test instance
        $component = Livewire::test(Register::class);

        // Mock user input
        $username = 'testUser';
        $email = 'test@email.com';
        $password = 'password123';

        // Act: Update the Livewire component's state and call the register method
        $component->set('username', $username)
            ->set('email', $email)
            ->set('password', $password)
            ->set('password_confirmation', $password)
            ->call('register');

        // Assert: Check if the user and Login records are created and attached
        $user = User::where('email', $email)->first();
        $login = Login::where('email', $email)->first();

        $this->assertNotNull($user);
        $this->assertNotNull($login);
        $this->assertTrue(Hash::check($password, $user->password));

        $this->assertDatabaseHas('user_logins', [
            'user_id' => $user->id,
            'login_id' => $login->account_id,
        ]);
    }
}
