<?php

namespace Tests\Unit\Notifications;

use App\Models\User;
use App\Notifications\WelcomeNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WelcomeNotificationTest extends TestCase
{
    #[Test]
    public function it_implements_should_queue(): void
    {
        $notification = new WelcomeNotification();

        $this->assertInstanceOf(ShouldQueue::class, $notification);
    }

    #[Test]
    public function it_sends_via_mail_channel(): void
    {
        $notification = new WelcomeNotification();
        $user = User::factory()->make();

        $channels = $notification->via($user);

        $this->assertContains('mail', $channels);
    }

    #[Test]
    public function it_has_correct_subject(): void
    {
        $notification = new WelcomeNotification();
        $user = User::factory()->make(['name' => 'TestPlayer']);

        $mailMessage = $notification->toMail($user);

        $this->assertInstanceOf(MailMessage::class, $mailMessage);
        $this->assertEquals('Welcome to XileRO! Your Adventure Begins', $mailMessage->subject);
    }

    #[Test]
    public function it_has_greeting(): void
    {
        $notification = new WelcomeNotification();
        $user = User::factory()->make(['name' => 'TestPlayer']);

        $mailMessage = $notification->toMail($user);

        $this->assertEquals('Hey there, Adventurer!', $mailMessage->greeting);
    }

    #[Test]
    public function it_includes_dashboard_action(): void
    {
        $notification = new WelcomeNotification();
        $user = User::factory()->make();

        $mailMessage = $notification->toMail($user);

        $this->assertEquals('Go to Dashboard', $mailMessage->actionText);
        $this->assertEquals(route('dashboard'), $mailMessage->actionUrl);
    }

    #[Test]
    public function it_includes_welcome_content(): void
    {
        $notification = new WelcomeNotification();
        $user = User::factory()->make();

        $mailMessage = $notification->toMail($user);

        // Check that introLines contains expected content
        $introText = implode(' ', $mailMessage->introLines);
        $this->assertStringContainsString('XileRO', $introText);
        $this->assertStringContainsString('game accounts', $introText);
    }

    #[Test]
    public function it_mentions_helpful_resources(): void
    {
        $notification = new WelcomeNotification();
        $user = User::factory()->make();

        $mailMessage = $notification->toMail($user);

        // The notification mentions various resources in the content
        $introText = implode(' ', $mailMessage->introLines);

        // Check for presence of helpful starter info (account info, getting started)
        $this->assertTrue(
            str_contains($introText, 'account') ||
            str_contains($introText, 'start') ||
            str_contains($introText, 'adventure') ||
            str_contains($introText, 'community') ||
            str_contains($introText, 'help')
        );
    }
}
