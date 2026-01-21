<?php

namespace Tests\Unit\Notifications;

use App\Models\UberShopPurchase;
use App\Models\User;
use App\Notifications\UberShopPurchaseNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Messages\MailMessage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UberShopPurchaseNotificationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_implements_should_queue(): void
    {
        $purchase = UberShopPurchase::factory()->make();
        $notification = new UberShopPurchaseNotification($purchase, 'TestAccount', 'XileRO');

        $this->assertInstanceOf(ShouldQueue::class, $notification);
    }

    #[Test]
    public function it_sends_via_mail_channel(): void
    {
        $purchase = UberShopPurchase::factory()->make();
        $notification = new UberShopPurchaseNotification($purchase, 'TestAccount', 'XileRO');
        $user = User::factory()->make();

        $channels = $notification->via($user);

        $this->assertContains('mail', $channels);
    }

    #[Test]
    public function it_has_subject_with_item_name(): void
    {
        $purchase = UberShopPurchase::factory()->make(['item_name' => 'Legendary Sword']);
        $notification = new UberShopPurchaseNotification($purchase, 'TestAccount', 'XileRO');
        $user = User::factory()->make();

        $mailMessage = $notification->toMail($user);

        $this->assertInstanceOf(MailMessage::class, $mailMessage);
        $this->assertStringContainsString('Legendary Sword', $mailMessage->subject);
    }

    #[Test]
    public function it_includes_item_name(): void
    {
        $purchase = UberShopPurchase::factory()->make(['item_name' => 'Legendary Sword']);
        $notification = new UberShopPurchaseNotification($purchase, 'TestAccount', 'XileRO');
        $user = User::factory()->make();

        $mailMessage = $notification->toMail($user);

        $introText = implode(' ', $mailMessage->introLines);
        $this->assertStringContainsString('Legendary Sword', $introText);
    }

    #[Test]
    public function it_includes_uber_cost(): void
    {
        $purchase = UberShopPurchase::factory()->make(['uber_cost' => 1500]);
        $notification = new UberShopPurchaseNotification($purchase, 'TestAccount', 'XileRO');
        $user = User::factory()->make();

        $mailMessage = $notification->toMail($user);

        $introText = implode(' ', $mailMessage->introLines);
        $this->assertStringContainsString('1500', $introText);
    }

    #[Test]
    public function it_includes_refine_level_when_present(): void
    {
        $purchase = UberShopPurchase::factory()->make([
            'item_name' => 'Legendary Sword',
            'refine_level' => 10,
        ]);
        $notification = new UberShopPurchaseNotification($purchase, 'TestAccount', 'XileRO');
        $user = User::factory()->make();

        $mailMessage = $notification->toMail($user);

        $this->assertStringContainsString('+10', $mailMessage->subject);
    }

    #[Test]
    public function it_stores_purchase_reference(): void
    {
        $purchase = UberShopPurchase::factory()->make();
        $notification = new UberShopPurchaseNotification($purchase, 'TestAccount', 'XileRO');

        $this->assertEquals($purchase, $notification->purchase);
        $this->assertEquals('TestAccount', $notification->gameAccountName);
        $this->assertEquals('XileRO', $notification->serverName);
    }

    #[Test]
    public function it_includes_server_name(): void
    {
        $purchase = UberShopPurchase::factory()->make();
        $notification = new UberShopPurchaseNotification($purchase, 'TestAccount', 'XileRO');
        $user = User::factory()->make();

        $mailMessage = $notification->toMail($user);

        $introText = implode(' ', $mailMessage->introLines);
        $this->assertStringContainsString('XileRO', $introText);
    }
}
