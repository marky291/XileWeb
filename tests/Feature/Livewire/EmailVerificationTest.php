<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Auth\VerifyEmail;
use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function verification_notice_page_is_accessible_for_unverified_users(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get(route('verification.notice'))
            ->assertStatus(200)
            ->assertSeeLivewire(VerifyEmail::class);
    }

    #[Test]
    public function verified_users_can_access_verification_notice(): void
    {
        // Note: The verification page doesn't redirect verified users -
        // it shows the page and they can proceed to dashboard from there
        $user = User::factory()->create(); // Factory creates verified users

        $this->actingAs($user)
            ->get(route('verification.notice'))
            ->assertStatus(200);
    }

    #[Test]
    public function guest_cannot_access_verification_notice(): void
    {
        $this->get(route('verification.notice'))
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function user_can_resend_verification_email(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();

        Livewire::actingAs($user)
            ->test(VerifyEmail::class)
            ->call('sendVerification');

        Notification::assertSentTo($user, VerifyEmailNotification::class);
    }

    #[Test]
    public function verified_user_cannot_resend_verification_email(): void
    {
        Notification::fake();

        $user = User::factory()->create(); // Already verified

        Livewire::actingAs($user)
            ->test(VerifyEmail::class)
            ->call('sendVerification');

        Notification::assertNothingSent();
    }

    #[Test]
    public function user_can_verify_email_with_valid_signed_url(): void
    {
        Event::fake([Verified::class]);

        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $this->actingAs($user)
            ->get($verificationUrl)
            ->assertRedirect(route('dashboard').'?verified=1');

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        Event::assertDispatched(Verified::class);
    }

    #[Test]
    public function user_cannot_verify_email_with_invalid_hash(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => 'invalid-hash']
        );

        $this->actingAs($user)
            ->get($verificationUrl)
            ->assertForbidden();

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    #[Test]
    public function user_cannot_verify_other_users_email(): void
    {
        $user = User::factory()->unverified()->create();
        $otherUser = User::factory()->unverified()->create();

        // Create URL for other user's email
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $otherUser->id, 'hash' => sha1($otherUser->email)]
        );

        $this->actingAs($user)
            ->get($verificationUrl)
            ->assertForbidden();

        $this->assertFalse($otherUser->fresh()->hasVerifiedEmail());
    }

    #[Test]
    public function verification_link_expires_after_60_minutes(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        // Travel forward 61 minutes
        $this->travel(61)->minutes();

        $this->actingAs($user)
            ->get($verificationUrl)
            ->assertForbidden();

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    #[Test]
    public function unsigned_verification_url_is_rejected(): void
    {
        $user = User::factory()->unverified()->create();

        // Try to access without proper signature
        $this->actingAs($user)
            ->get(route('verification.verify', [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]))
            ->assertForbidden();
    }

    #[Test]
    public function already_verified_user_is_redirected_on_verification_attempt(): void
    {
        $user = User::factory()->create(); // Already verified

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $this->actingAs($user)
            ->get($verificationUrl)
            ->assertRedirect(route('dashboard').'?verified=1');
    }

    #[Test]
    public function unverified_users_are_redirected_to_verification_from_protected_routes(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('verification.notice'));
    }

    #[Test]
    public function verification_email_contains_correct_link(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();

        $user->sendEmailVerificationNotification();

        Notification::assertSentTo($user, VerifyEmailNotification::class, function ($notification) use ($user) {
            $mail = $notification->toMail($user);

            // Verify the action URL is a signed route
            $this->assertStringContainsString('/verify-email/', $mail->actionUrl);
            $this->assertStringContainsString('signature=', $mail->actionUrl);

            return true;
        });
    }

    #[Test]
    public function resend_verification_is_throttled(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();

        // Clear any existing rate limits
        RateLimiter::clear('verify-email:'.$user->id);

        // First request should succeed
        $component = Livewire::actingAs($user)
            ->test(VerifyEmail::class)
            ->call('sendVerification');

        Notification::assertSentToTimes($user, VerifyEmailNotification::class, 1);

        // Second request (same component) should be throttled
        $component->call('sendVerification');

        // Should still only have sent one notification
        Notification::assertSentToTimes($user, VerifyEmailNotification::class, 1);
    }

    #[Test]
    public function throttle_resets_after_timeout(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();

        // Clear any existing rate limits
        RateLimiter::clear('verify-email:'.$user->id);

        // First request
        Livewire::actingAs($user)
            ->test(VerifyEmail::class)
            ->call('sendVerification');

        Notification::assertSentToTimes($user, VerifyEmailNotification::class, 1);

        // Travel past throttle timeout (60 seconds)
        $this->travel(61)->seconds();

        // Should be able to send again
        Livewire::actingAs($user)
            ->test(VerifyEmail::class)
            ->call('sendVerification');

        Notification::assertSentToTimes($user, VerifyEmailNotification::class, 2);
    }

    #[Test]
    public function verification_page_shows_user_email(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'test@example.com',
        ]);

        Livewire::actingAs($user)
            ->test(VerifyEmail::class)
            ->assertSee('test@example.com');
    }

    #[Test]
    public function verification_page_shows_helpful_info(): void
    {
        $user = User::factory()->unverified()->create();

        Livewire::actingAs($user)
            ->test(VerifyEmail::class)
            ->assertSee('5 minutes')
            ->assertSee('spam or junk folder');
    }
}
