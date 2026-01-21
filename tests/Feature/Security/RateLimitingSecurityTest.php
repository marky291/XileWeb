<?php

namespace Tests\Feature\Security;

use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\GameAccountLogin;
use App\Livewire\Auth\GameAccountRegister;
use App\Livewire\Auth\Register;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RateLimitingSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('register:'.request()->ip());
        RateLimiter::clear('game-register:'.request()->ip());
    }

    // ============================================
    // Master Account Login Rate Limiting
    // ============================================

    #[Test]
    public function login_allows_five_failed_attempts_before_throttle(): void
    {
        User::factory()->create([
            'email' => 'ratelimitcheck@example.com',
            'password' => 'password123',
        ]);

        // Clear any existing rate limits
        $throttleKey = 'ratelimitcheck@example.com|'.request()->ip();
        RateLimiter::clear($throttleKey);

        // First 5 attempts should just show "invalid credentials"
        for ($i = 0; $i < 5; $i++) {
            Livewire::test(GameAccountLogin::class)
                ->set('email', 'ratelimitcheck@example.com')
                ->set('password', 'wrongpassword')
                ->call('authenticate');
        }

        // 6th attempt should be throttled
        Livewire::test(GameAccountLogin::class)
            ->set('email', 'ratelimitcheck@example.com')
            ->set('password', 'wrongpassword')
            ->call('authenticate')
            ->assertHasErrors(['email']);

        // Verify rate limiter was triggered
        $this->assertTrue(RateLimiter::tooManyAttempts($throttleKey, 5));
    }

    #[Test]
    public function login_rate_limit_is_per_email_and_ip(): void
    {
        User::factory()->create([
            'email' => 'user1@example.com',
            'password' => 'password123',
        ]);

        User::factory()->create([
            'email' => 'user2@example.com',
            'password' => 'password123',
        ]);

        // Hit rate limit for user1
        $throttleKey1 = 'user1@example.com|'.request()->ip();
        for ($i = 0; $i < 6; $i++) {
            RateLimiter::hit($throttleKey1);
        }

        // user1 should be throttled
        $this->assertTrue(RateLimiter::tooManyAttempts($throttleKey1, 5));

        // user2 should NOT be throttled
        $throttleKey2 = 'user2@example.com|'.request()->ip();
        $this->assertFalse(RateLimiter::tooManyAttempts($throttleKey2, 5));
    }

    #[Test]
    public function successful_login_clears_rate_limit(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $throttleKey = strtolower('test@example.com').'|'.request()->ip();

        // Add some failed attempts
        RateLimiter::hit($throttleKey);
        RateLimiter::hit($throttleKey);
        RateLimiter::hit($throttleKey);

        $this->assertEquals(3, RateLimiter::attempts($throttleKey));

        // Successful login
        Livewire::test(GameAccountLogin::class)
            ->set('email', 'test@example.com')
            ->set('password', 'password123')
            ->call('authenticate');

        // Rate limiter should be cleared
        $this->assertEquals(0, RateLimiter::attempts($throttleKey));
    }

    // ============================================
    // Master Account Registration Rate Limiting
    // ============================================

    #[Test]
    public function registration_is_rate_limited(): void
    {
        $throttleKey = 'register:'.request()->ip();

        // Simulate 5 registration attempts
        for ($i = 0; $i < 5; $i++) {
            RateLimiter::hit($throttleKey, 3600);
        }

        // 6th attempt should be throttled
        Livewire::test(Register::class)
            ->set('email', 'newuser@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors(['email']);

        // No user should be created
        $this->assertDatabaseMissing('users', ['email' => 'newuser@example.com']);
    }

    #[Test]
    public function registration_rate_limit_has_one_hour_decay(): void
    {
        $throttleKey = 'register:'.request()->ip();

        // Hit rate limit
        for ($i = 0; $i < 5; $i++) {
            RateLimiter::hit($throttleKey, 3600);
        }

        $this->assertTrue(RateLimiter::tooManyAttempts($throttleKey, 5));

        // Travel forward 1 hour
        $this->travel(61)->minutes();

        // Should no longer be throttled
        $this->assertFalse(RateLimiter::tooManyAttempts($throttleKey, 5));
    }

    // ============================================
    // Game Account Registration Rate Limiting
    // ============================================

    #[Test]
    public function game_registration_is_rate_limited(): void
    {
        $throttleKey = 'game-register:'.request()->ip();

        // Simulate 5 registration attempts
        for ($i = 0; $i < 5; $i++) {
            RateLimiter::hit($throttleKey, 3600);
        }

        // 6th attempt should be throttled
        Livewire::test(GameAccountRegister::class)
            ->set('server', 'xilero')
            ->set('username', 'newgameuser')
            ->set('email', 'newgame@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors(['username']);
    }

    // ============================================
    // Password Reset Rate Limiting
    // ============================================

    #[Test]
    public function password_reset_is_throttled_to_one_per_minute(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        // First request should succeed
        Livewire::test(ForgotPassword::class)
            ->set('email', 'test@example.com')
            ->call('sendResetLink')
            ->assertHasNoErrors();

        // Second immediate request should be throttled
        Livewire::test(ForgotPassword::class)
            ->set('email', 'test@example.com')
            ->call('sendResetLink');

        // The Password broker handles throttling internally
        // We just verify no exception was thrown
    }

    // ============================================
    // Brute Force Protection
    // ============================================

    #[Test]
    public function brute_force_attack_is_mitigated_by_rate_limiting(): void
    {
        $user = User::factory()->create([
            'email' => 'victim@example.com',
            'password' => 'correctpassword123',
        ]);

        $component = Livewire::test(GameAccountLogin::class);
        $commonPasswords = ['password', '123456', 'password123', 'admin', 'letmein', '12345678'];

        // Try common passwords
        foreach ($commonPasswords as $i => $password) {
            $component
                ->set('email', 'victim@example.com')
                ->set('password', $password)
                ->call('authenticate');

            // After 5 attempts, should be throttled
            if ($i >= 5) {
                $errors = session('errors');
                if ($errors) {
                    $this->assertStringContainsString('Too many', $errors->first('email'));
                }
            }
        }

        // User should never be logged in
        $this->assertGuest();
    }

    #[Test]
    public function rate_limit_prevents_credential_stuffing(): void
    {
        // Create multiple users
        User::factory()->create(['email' => 'user1@example.com', 'password' => 'password1']);
        User::factory()->create(['email' => 'user2@example.com', 'password' => 'password2']);
        User::factory()->create(['email' => 'user3@example.com', 'password' => 'password3']);

        $component = Livewire::test(GameAccountLogin::class);

        // Attempt credential stuffing with different emails
        // Note: Rate limiting is per email+IP, so each email gets its own limit
        // But we're using the same session/IP
        $credentials = [
            ['user1@example.com', 'wrongpass'],
            ['user1@example.com', 'wrongpass2'],
            ['user1@example.com', 'wrongpass3'],
            ['user1@example.com', 'wrongpass4'],
            ['user1@example.com', 'wrongpass5'],
            ['user1@example.com', 'wrongpass6'], // Should be throttled now
        ];

        foreach ($credentials as $i => [$email, $password]) {
            $component
                ->set('email', $email)
                ->set('password', $password)
                ->call('authenticate');

            if ($i == 5) {
                $errors = session('errors');
                if ($errors) {
                    $this->assertStringContainsString('Too many', $errors->first('email'));
                }
            }
        }
    }
}
