<?php

namespace Tests\Unit\Notifications;

use App\Models\GameAccount;
use App\Models\User;
use App\Notifications\GameAccountPasswordResetNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Messages\MailMessage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GameAccountPasswordResetNotificationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_implements_should_queue(): void
    {
        $gameAccount = GameAccount::factory()->make();
        $notification = new GameAccountPasswordResetNotification($gameAccount);

        $this->assertInstanceOf(ShouldQueue::class, $notification);
    }

    #[Test]
    public function it_sends_via_mail_channel(): void
    {
        $gameAccount = GameAccount::factory()->make();
        $notification = new GameAccountPasswordResetNotification($gameAccount);
        $user = User::factory()->make();

        $channels = $notification->via($user);

        $this->assertContains('mail', $channels);
    }

    #[Test]
    public function it_has_correct_subject(): void
    {
        $gameAccount = GameAccount::factory()->make();
        $notification = new GameAccountPasswordResetNotification($gameAccount);
        $user = User::factory()->make();

        $mailMessage = $notification->toMail($user);

        $this->assertInstanceOf(MailMessage::class, $mailMessage);
        $this->assertEquals('Game Account Password Changed', $mailMessage->subject);
    }

    #[Test]
    public function it_includes_game_account_username(): void
    {
        $gameAccount = GameAccount::factory()->make(['userid' => 'TestPlayer123']);
        $notification = new GameAccountPasswordResetNotification($gameAccount);
        $user = User::factory()->make();

        $mailMessage = $notification->toMail($user);

        $introText = implode(' ', $mailMessage->introLines);
        $this->assertStringContainsString('TestPlayer123', $introText);
    }

    #[Test]
    public function it_includes_security_warning(): void
    {
        $gameAccount = GameAccount::factory()->make();
        $notification = new GameAccountPasswordResetNotification($gameAccount);
        $user = User::factory()->make();

        $mailMessage = $notification->toMail($user);

        $allText = implode(' ', $mailMessage->introLines);
        // Should mention contacting support if not authorized
        $this->assertStringContainsString('contact', strtolower($allText));
    }

    #[Test]
    public function it_stores_game_account_reference(): void
    {
        $gameAccount = GameAccount::factory()->make();
        $notification = new GameAccountPasswordResetNotification($gameAccount);

        $this->assertEquals($gameAccount, $notification->gameAccount);
    }
}
