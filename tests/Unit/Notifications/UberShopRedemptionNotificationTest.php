<?php

namespace Tests\Unit\Notifications;

use App\Models\UberShopPurchase;
use App\Models\User;
use App\Notifications\UberShopRedemptionNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Messages\MailMessage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UberShopRedemptionNotificationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_implements_should_queue(): void
    {
        $purchase = UberShopPurchase::factory()->make();
        $notification = new UberShopRedemptionNotification($purchase, 'XileRO');

        $this->assertInstanceOf(ShouldQueue::class, $notification);
    }

    #[Test]
    public function it_sends_via_mail_channel(): void
    {
        $purchase = UberShopPurchase::factory()->make();
        $notification = new UberShopRedemptionNotification($purchase, 'XileRO');
        $user = User::factory()->make();

        $channels = $notification->via($user);

        $this->assertContains('mail', $channels);
    }

    #[Test]
    public function it_has_subject_with_item_name(): void
    {
        $purchase = UberShopPurchase::factory()->make(['item_name' => 'Magic Staff']);
        $notification = new UberShopRedemptionNotification($purchase, 'XileRO');
        $user = User::factory()->make();

        $mailMessage = $notification->toMail($user);

        $this->assertInstanceOf(MailMessage::class, $mailMessage);
        $this->assertStringContainsString('Magic Staff', $mailMessage->subject);
    }

    #[Test]
    public function it_includes_item_name(): void
    {
        $purchase = UberShopPurchase::factory()->make(['item_name' => 'Magic Staff']);
        $notification = new UberShopRedemptionNotification($purchase, 'XileRO');
        $user = User::factory()->make();

        $mailMessage = $notification->toMail($user);

        $introText = implode(' ', $mailMessage->introLines);
        $this->assertStringContainsString('Magic Staff', $introText);
    }

    #[Test]
    public function it_includes_character_name(): void
    {
        $purchase = UberShopPurchase::factory()->make(['claimed_by_char_name' => 'MyHero']);
        $notification = new UberShopRedemptionNotification($purchase, 'XileRO');
        $user = User::factory()->make();

        $mailMessage = $notification->toMail($user);

        $introText = implode(' ', $mailMessage->introLines);
        $this->assertStringContainsString('MyHero', $introText);
    }

    #[Test]
    public function it_includes_refund_info(): void
    {
        $purchase = UberShopPurchase::factory()->make([
            'claimed_at' => now(),
        ]);
        $notification = new UberShopRedemptionNotification($purchase, 'XileRO');
        $user = User::factory()->make();

        $mailMessage = $notification->toMail($user);

        $allText = implode(' ', $mailMessage->introLines);
        $this->assertStringContainsString('Refund', $allText);
    }

    #[Test]
    public function it_stores_purchase_reference(): void
    {
        $purchase = UberShopPurchase::factory()->make();
        $notification = new UberShopRedemptionNotification($purchase, 'XileRO', 48);

        $this->assertEquals($purchase, $notification->purchase);
        $this->assertEquals('XileRO', $notification->serverName);
        $this->assertEquals(48, $notification->refundHours);
    }

    #[Test]
    public function it_defaults_refund_hours_to_24(): void
    {
        $purchase = UberShopPurchase::factory()->make();
        $notification = new UberShopRedemptionNotification($purchase, 'XileRO');

        $this->assertEquals(24, $notification->refundHours);
    }
}
