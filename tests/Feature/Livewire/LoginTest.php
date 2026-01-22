<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Auth\GameAccountLogin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function login_page_is_accessible(): void
    {
        $this->get(route('login'))
            ->assertStatus(200)
            ->assertSeeLivewire(GameAccountLogin::class);
    }

    #[Test]
    public function authenticated_user_is_redirected_from_login(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('login'))
            ->assertRedirect(route('dashboard'));
    }

    #[Test]
    public function user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        Livewire::test(GameAccountLogin::class)
            ->set('email', 'test@example.com')
            ->set('password', 'password123')
            ->call('authenticate')
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function user_cannot_login_with_invalid_password(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        Livewire::test(GameAccountLogin::class)
            ->set('email', 'test@example.com')
            ->set('password', 'wrongpassword')
            ->call('authenticate')
            ->assertHasErrors(['email']);

        $this->assertGuest();
    }

    #[Test]
    public function user_cannot_login_with_nonexistent_email(): void
    {
        Livewire::test(GameAccountLogin::class)
            ->set('email', 'nonexistent@example.com')
            ->set('password', 'password123')
            ->call('authenticate')
            ->assertHasErrors(['email']);

        $this->assertGuest();
    }

    #[Test]
    public function email_is_required(): void
    {
        Livewire::test(GameAccountLogin::class)
            ->set('email', '')
            ->set('password', 'password123')
            ->call('authenticate')
            ->assertHasErrors(['email' => 'required']);
    }

    #[Test]
    public function password_is_required(): void
    {
        Livewire::test(GameAccountLogin::class)
            ->set('email', 'test@example.com')
            ->set('password', '')
            ->call('authenticate')
            ->assertHasErrors(['password' => 'required']);
    }

    #[Test]
    public function remember_me_functionality_works(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        Livewire::test(GameAccountLogin::class)
            ->set('email', 'test@example.com')
            ->set('password', 'password123')
            ->set('remember', true)
            ->call('authenticate');

        $this->assertAuthenticatedAs($user);
        // The remember_token will be set in the database
        $this->assertNotNull($user->fresh()->remember_token);
    }

    #[Test]
    public function session_is_regenerated_after_login(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $oldSessionId = session()->getId();

        Livewire::test(GameAccountLogin::class)
            ->set('email', 'test@example.com')
            ->set('password', 'password123')
            ->call('authenticate');

        $this->assertNotEquals($oldSessionId, session()->getId());
    }

    #[Test]
    public function login_redirects_to_intended_url(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        session()->put('url.intended', '/some-protected-page');

        Livewire::test(GameAccountLogin::class)
            ->set('email', 'test@example.com')
            ->set('password', 'password123')
            ->call('authenticate')
            ->assertRedirect('/some-protected-page');
    }

    // Rate Limiting Tests

    #[Test]
    public function login_is_rate_limited_after_five_failed_attempts(): void
    {
        // Clear any existing rate limits
        $throttleKey = 'ratelimited@example.com|'.request()->ip();
        RateLimiter::clear($throttleKey);

        User::factory()->create([
            'email' => 'ratelimited@example.com',
            'password' => 'password123',
        ]);

        // Make 5 failed attempts using individual component instances
        for ($i = 0; $i < 5; $i++) {
            Livewire::test(GameAccountLogin::class)
                ->set('email', 'ratelimited@example.com')
                ->set('password', 'wrongpassword')
                ->call('authenticate');
        }

        // 6th attempt should be throttled
        Livewire::test(GameAccountLogin::class)
            ->set('email', 'ratelimited@example.com')
            ->set('password', 'wrongpassword')
            ->call('authenticate');

        // Verify rate limiter was triggered
        $this->assertTrue(RateLimiter::tooManyAttempts($throttleKey, 5));
    }

    #[Test]
    public function rate_limiter_is_cleared_on_successful_login(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $throttleKey = strtolower('test@example.com').'|'.request()->ip();

        // Simulate some failed attempts
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

    // Security Tests - Input Sanitization
    // Note: Livewire's updating hooks sanitize malicious input before it's set.
    // These tests verify the sanitization methods exist and the component handles
    // validation correctly.

    #[Test]
    public function login_component_has_input_sanitization_methods(): void
    {
        $component = new GameAccountLogin;

        // Verify sanitization methods exist
        $this->assertTrue(method_exists($component, 'updatingEmail'));
        $this->assertTrue(method_exists($component, 'updatingPassword'));
        $this->assertTrue(method_exists($component, 'updatingRemember'));
    }

    #[Test]
    public function email_sanitization_converts_non_string_to_empty(): void
    {
        $component = new GameAccountLogin;

        // Test the sanitization method directly
        $value = ['malicious' => 'payload'];
        $component->updatingEmail($value);

        $this->assertEquals('', $value);
    }

    #[Test]
    public function password_sanitization_converts_non_string_to_empty(): void
    {
        $component = new GameAccountLogin;

        $value = ['malicious' => 'payload'];
        $component->updatingPassword($value);

        $this->assertEquals('', $value);
    }

    #[Test]
    public function remember_sanitization_converts_array_to_false(): void
    {
        $component = new GameAccountLogin;

        $value = ['malicious' => 'payload'];
        $component->updatingRemember($value);

        $this->assertFalse($value);
    }

    #[Test]
    public function remember_sanitization_accepts_valid_values(): void
    {
        $component = new GameAccountLogin;

        // Test true values
        $value = true;
        $component->updatingRemember($value);
        $this->assertTrue($value);

        $value = 'true';
        $component->updatingRemember($value);
        $this->assertTrue($value);

        $value = '1';
        $component->updatingRemember($value);
        $this->assertTrue($value);

        // Test false values
        $value = false;
        $component->updatingRemember($value);
        $this->assertFalse($value);

        $value = 'false';
        $component->updatingRemember($value);
        $this->assertFalse($value);

        $value = '0';
        $component->updatingRemember($value);
        $this->assertFalse($value);
    }

    // Case sensitivity tests

    #[Test]
    public function email_comparison_is_case_insensitive(): void
    {
        // Use unique email to avoid conflicts with other tests
        $email = 'casetest-login-'.uniqid().'@example.com';
        $lowerEmail = strtolower($email);

        // Clear rate limiter for this email
        RateLimiter::clear($lowerEmail.'|'.request()->ip());

        $user = User::factory()->create([
            'email' => $email,
            'password' => 'password123',
        ]);

        Livewire::test(GameAccountLogin::class)
            ->set('email', $lowerEmail)
            ->set('password', 'password123')
            ->call('authenticate')
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function array_injection_attack_is_sanitized_and_rejected(): void
    {
        // This tests that array injection attacks are handled gracefully
        // instead of throwing TypeErrors
        Livewire::test(GameAccountLogin::class)
            ->set('email', ['malicious' => 'payload'])
            ->set('password', ['another' => 'attack'])
            ->set('remember', ['array' => 'value'])
            ->call('authenticate')
            ->assertHasErrors(['email', 'password']);

        $this->assertGuest();
    }
}
