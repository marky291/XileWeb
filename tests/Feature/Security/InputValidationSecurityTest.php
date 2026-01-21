<?php

namespace Tests\Feature\Security;

use App\Livewire\Auth\Dashboard;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\GameAccountLogin;
use App\Livewire\Auth\GameAccountRegister;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InputValidationSecurityTest extends TestCase
{
    use RefreshDatabase;

    // ============================================
    // Input Sanitization Method Tests
    // These test the sanitization methods directly to ensure
    // array injection attacks are prevented.
    // ============================================

    #[Test]
    public function register_component_has_input_sanitization_methods(): void
    {
        $component = new Register();

        $this->assertTrue(method_exists($component, 'updatingEmail'));
        $this->assertTrue(method_exists($component, 'updatingPassword'));
        $this->assertTrue(method_exists($component, 'updatingPasswordConfirmation'));
    }

    #[Test]
    public function register_email_sanitization_works(): void
    {
        $component = new Register();

        $value = ['malicious' => 'payload'];
        $component->updatingEmail($value);
        $this->assertEquals('', $value);

        $value = 'valid@email.com';
        $component->updatingEmail($value);
        $this->assertEquals('valid@email.com', $value);
    }

    #[Test]
    public function register_password_sanitization_works(): void
    {
        $component = new Register();

        $value = ['malicious' => 'payload'];
        $component->updatingPassword($value);
        $this->assertEquals('', $value);
    }

    #[Test]
    public function login_component_has_input_sanitization_methods(): void
    {
        $component = new GameAccountLogin();

        $this->assertTrue(method_exists($component, 'updatingEmail'));
        $this->assertTrue(method_exists($component, 'updatingPassword'));
        $this->assertTrue(method_exists($component, 'updatingRemember'));
    }

    #[Test]
    public function forgot_password_component_has_sanitization_methods(): void
    {
        $component = new ForgotPassword();

        $this->assertTrue(method_exists($component, 'updatingEmail'));

        $value = ['malicious' => 'payload'];
        $component->updatingEmail($value);
        $this->assertEquals('', $value);
    }

    #[Test]
    public function game_register_component_has_sanitization_methods(): void
    {
        $component = new GameAccountRegister();

        $this->assertTrue(method_exists($component, 'updatingServer'));
        $this->assertTrue(method_exists($component, 'updatingUsername'));
        $this->assertTrue(method_exists($component, 'updatingEmail'));
        $this->assertTrue(method_exists($component, 'updatingPassword'));
        $this->assertTrue(method_exists($component, 'updatingPasswordConfirmation'));
    }

    #[Test]
    public function dashboard_component_has_sanitization_methods(): void
    {
        $component = new Dashboard();

        $this->assertTrue(method_exists($component, 'updatingGameServer'));
        $this->assertTrue(method_exists($component, 'updatingGameUsername'));
        $this->assertTrue(method_exists($component, 'updatingGamePassword'));
        $this->assertTrue(method_exists($component, 'updatingGamePasswordConfirmation'));
    }

    // ============================================
    // Registration Validation Tests
    // ============================================

    #[Test]
    public function registration_rejects_invalid_email_format(): void
    {
        Livewire::test(Register::class)
            ->set('email', 'not-an-email')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors(['email' => 'email']);
    }

    #[Test]
    public function registration_rejects_blocked_email(): void
    {
        Livewire::test(Register::class)
            ->set('email', 'a@a.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors(['email']);
    }

    #[Test]
    public function registration_enforces_password_minimum_length(): void
    {
        Livewire::test(Register::class)
            ->set('email', 'test@example.com')
            ->set('password', 'short')
            ->set('password_confirmation', 'short')
            ->call('register')
            ->assertHasErrors(['password']);
    }

    #[Test]
    public function registration_enforces_password_confirmation(): void
    {
        Livewire::test(Register::class)
            ->set('email', 'test@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'differentpassword')
            ->call('register')
            ->assertHasErrors(['password']);
    }

    // ============================================
    // Forgot Password Validation
    // ============================================

    #[Test]
    public function forgot_password_rejects_invalid_email(): void
    {
        Livewire::test(ForgotPassword::class)
            ->set('email', 'not-an-email')
            ->call('sendResetLink')
            ->assertHasErrors(['email' => 'email']);
    }

    // ============================================
    // Reset Password Validation
    // ============================================

    #[Test]
    public function reset_password_enforces_minimum_length(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        Livewire::test(ResetPassword::class, ['token' => $token])
            ->set('email', $user->email)
            ->set('password', 'short')
            ->set('password_confirmation', 'short')
            ->call('resetPassword')
            ->assertHasErrors(['password']);
    }

    // ============================================
    // Game Account Registration Validation
    // ============================================

    #[Test]
    public function game_register_rejects_invalid_server(): void
    {
        Livewire::test(GameAccountRegister::class)
            ->set('server', 'invalid_server')
            ->set('username', 'testuser')
            ->set('email', 'test@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors(['server']);
    }

    #[Test]
    public function game_register_username_must_be_alphanumeric(): void
    {
        Livewire::test(GameAccountRegister::class)
            ->set('server', 'xilero')
            ->set('username', 'user with spaces!')
            ->set('email', 'test@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors(['username']);
    }

    #[Test]
    public function game_register_username_length_is_enforced(): void
    {
        // Too short (min 4)
        Livewire::test(GameAccountRegister::class)
            ->set('server', 'xilero')
            ->set('username', 'abc')
            ->set('email', 'test@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors(['username']);

        // Too long (max 23)
        Livewire::test(GameAccountRegister::class)
            ->set('server', 'xilero')
            ->set('username', str_repeat('a', 24))
            ->set('email', 'test@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors(['username']);
    }

    #[Test]
    public function game_register_password_length_is_enforced(): void
    {
        // Too short (min 6)
        Livewire::test(GameAccountRegister::class)
            ->set('server', 'xilero')
            ->set('username', 'testuser')
            ->set('email', 'test@example.com')
            ->set('password', '12345')
            ->set('password_confirmation', '12345')
            ->call('register')
            ->assertHasErrors(['password']);

        // Too long (max 31)
        Livewire::test(GameAccountRegister::class)
            ->set('server', 'xilero')
            ->set('username', 'testuser')
            ->set('email', 'test@example.com')
            ->set('password', str_repeat('a', 32))
            ->set('password_confirmation', str_repeat('a', 32))
            ->call('register')
            ->assertHasErrors(['password']);
    }

    #[Test]
    public function game_register_email_max_length_is_enforced(): void
    {
        // Email too long (max 39 for game DB)
        Livewire::test(GameAccountRegister::class)
            ->set('server', 'xilero')
            ->set('username', 'testuser')
            ->set('email', str_repeat('a', 30).'@example.com') // 42 chars
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors(['email']);
    }

    // ============================================
    // SQL Injection Prevention Tests
    // ============================================

    #[Test]
    public function login_email_sql_injection_is_handled(): void
    {
        // Create a legit user
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        // Attempt SQL injection - should fail safely without error
        Livewire::test(GameAccountLogin::class)
            ->set('email', "' OR '1'='1")
            ->set('password', "' OR '1'='1")
            ->call('authenticate')
            ->assertHasErrors(['email']);

        $this->assertGuest();
    }

    #[Test]
    public function registration_email_sql_injection_is_handled(): void
    {
        Livewire::test(Register::class)
            ->set('email', "test'; DROP TABLE users; --@example.com")
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors(['email']);

        // Table should still exist
        $this->assertDatabaseCount('users', 0);
    }

    #[Test]
    public function game_username_sql_injection_is_handled(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->set('showCreateForm', true)
            ->set('gameServer', 'xilero')
            ->set('gameUsername', "admin'; DROP TABLE login; --")
            ->set('gamePassword', 'password123')
            ->set('gamePassword_confirmation', 'password123')
            ->call('createGameAccount')
            ->assertHasErrors(['gameUsername']); // Should fail validation (not alphanumeric)
    }

    // ============================================
    // XSS Prevention Tests
    // ============================================

    #[Test]
    public function xss_in_username_is_escaped_in_output(): void
    {
        $user = User::factory()->create([
            'name' => '<script>alert("xss")</script>',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        // The script tag should be escaped, not rendered
        $response->assertDontSee('<script>alert("xss")</script>', false);
    }
}
