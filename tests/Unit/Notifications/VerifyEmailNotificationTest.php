<?php

namespace Tests\Unit\Notifications;

use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Messages\MailMessage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VerifyEmailNotificationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_implements_should_queue(): void
    {
        $notification = new VerifyEmailNotification();

        $this->assertInstanceOf(ShouldQueue::class, $notification);
    }

    #[Test]
    public function it_sends_via_mail_channel(): void
    {
        $notification = new VerifyEmailNotification();
        $user = User::factory()->make();

        $channels = $notification->via($user);

        $this->assertContains('mail', $channels);
    }

    #[Test]
    public function it_has_correct_subject(): void
    {
        $notification = new VerifyEmailNotification();
        $user = User::factory()->create();

        $mailMessage = $notification->toMail($user);

        $this->assertInstanceOf(MailMessage::class, $mailMessage);
        $this->assertEquals('Verify Your Email - XileRO', $mailMessage->subject);
    }

    #[Test]
    public function it_includes_verification_action(): void
    {
        $notification = new VerifyEmailNotification();
        $user = User::factory()->create();

        $mailMessage = $notification->toMail($user);

        $this->assertEquals('Verify Email Address', $mailMessage->actionText);
        $this->assertNotEmpty($mailMessage->actionUrl);
    }

    #[Test]
    public function it_generates_signed_url(): void
    {
        $notification = new VerifyEmailNotification();
        $user = User::factory()->create();

        $mailMessage = $notification->toMail($user);

        // Signed URLs contain signature parameter
        $this->assertStringContainsString('signature=', $mailMessage->actionUrl);
    }

    #[Test]
    public function it_mentions_expiration(): void
    {
        $notification = new VerifyEmailNotification();
        $user = User::factory()->create();

        $mailMessage = $notification->toMail($user);

        // The expiration message is in introLines
        $allText = implode(' ', array_merge($mailMessage->introLines, $mailMessage->outroLines ?? []));
        // Check for common expiration-related words
        $this->assertTrue(
            str_contains(strtolower($allText), 'expire') ||
            str_contains(strtolower($allText), 'minutes') ||
            str_contains(strtolower($allText), '60')
        );
    }
}
