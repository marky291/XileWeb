<?php

namespace Tests\Unit\Notifications;

use App\Models\User;
use App\Notifications\DiscordLinkedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DiscordLinkedNotificationTest extends TestCase
{
    #[Test]
    public function it_implements_should_queue(): void
    {
        $notification = new DiscordLinkedNotification('TestUser#1234');

        $this->assertInstanceOf(ShouldQueue::class, $notification);
    }

    #[Test]
    public function it_sends_via_mail_channel(): void
    {
        $notification = new DiscordLinkedNotification('TestUser#1234');
        $user = User::factory()->make();

        $channels = $notification->via($user);

        $this->assertContains('mail', $channels);
    }

    #[Test]
    public function it_has_correct_subject(): void
    {
        $notification = new DiscordLinkedNotification('TestUser#1234');
        $user = User::factory()->make();

        $mailMessage = $notification->toMail($user);

        $this->assertInstanceOf(MailMessage::class, $mailMessage);
        $this->assertEquals('Discord Account Linked', $mailMessage->subject);
    }

    #[Test]
    public function it_includes_discord_username(): void
    {
        $notification = new DiscordLinkedNotification('TestUser#1234');
        $user = User::factory()->make();

        $mailMessage = $notification->toMail($user);

        $introText = implode(' ', $mailMessage->introLines);
        $this->assertStringContainsString('TestUser#1234', $introText);
    }

    #[Test]
    public function it_includes_security_warning(): void
    {
        $notification = new DiscordLinkedNotification('TestUser#1234');
        $user = User::factory()->make();

        $mailMessage = $notification->toMail($user);

        $allText = implode(' ', array_merge($mailMessage->introLines, $mailMessage->outroLines));
        // Should mention contacting support if not authorized
        $this->assertStringContainsString('contact', strtolower($allText));
    }

    #[Test]
    public function it_stores_discord_username(): void
    {
        $notification = new DiscordLinkedNotification('TestUser#1234');

        $this->assertEquals('TestUser#1234', $notification->discordUsername);
    }
}
