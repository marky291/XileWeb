<?php

namespace Tests\Unit\Notifications;

use App\Models\User;
use App\Notifications\DonationAppliedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DonationAppliedNotificationTest extends TestCase
{
    #[Test]
    public function it_implements_should_queue(): void
    {
        $notification = new DonationAppliedNotification(
            amount: 25.00,
            totalUbers: 500,
            newBalance: 1500,
            paymentMethod: 'paypal',
        );

        $this->assertInstanceOf(ShouldQueue::class, $notification);
    }

    #[Test]
    public function it_sends_via_mail_channel(): void
    {
        $notification = new DonationAppliedNotification(
            amount: 25.00,
            totalUbers: 500,
            newBalance: 1500,
            paymentMethod: 'paypal',
        );
        $user = User::factory()->make();

        $channels = $notification->via($user);

        $this->assertContains('mail', $channels);
    }

    #[Test]
    public function it_has_correct_subject(): void
    {
        $notification = new DonationAppliedNotification(
            amount: 25.00,
            totalUbers: 500,
            newBalance: 1500,
            paymentMethod: 'paypal',
        );
        $user = User::factory()->make();

        $mailMessage = $notification->toMail($user);

        $this->assertInstanceOf(MailMessage::class, $mailMessage);
        $this->assertEquals('Thank You for Your Donation!', $mailMessage->subject);
    }

    #[Test]
    public function it_includes_donation_amount_in_view_data(): void
    {
        $notification = new DonationAppliedNotification(
            amount: 25.00,
            totalUbers: 500,
            newBalance: 1500,
            paymentMethod: 'paypal',
        );
        $user = User::factory()->make();

        $mailMessage = $notification->toMail($user);

        $this->assertEquals(25.00, $mailMessage->viewData['amount']);
    }

    #[Test]
    public function it_includes_ubers_received_in_view_data(): void
    {
        $notification = new DonationAppliedNotification(
            amount: 25.00,
            totalUbers: 500,
            newBalance: 1500,
            paymentMethod: 'paypal',
        );
        $user = User::factory()->make();

        $mailMessage = $notification->toMail($user);

        $this->assertEquals(500, $mailMessage->viewData['totalUbers']);
    }

    #[Test]
    public function it_includes_new_balance_in_view_data(): void
    {
        $notification = new DonationAppliedNotification(
            amount: 25.00,
            totalUbers: 500,
            newBalance: 1500,
            paymentMethod: 'paypal',
        );
        $user = User::factory()->make();

        $mailMessage = $notification->toMail($user);

        $this->assertEquals(1500, $mailMessage->viewData['newBalance']);
    }

    #[Test]
    public function it_includes_user_in_view_data(): void
    {
        $notification = new DonationAppliedNotification(
            amount: 25.00,
            totalUbers: 500,
            newBalance: 1500,
            paymentMethod: 'paypal',
        );
        $user = User::factory()->make(['name' => 'TestDonor']);

        $mailMessage = $notification->toMail($user);

        $this->assertEquals('TestDonor', $mailMessage->viewData['notifiable']->name);
    }

    #[Test]
    public function it_includes_shop_url_when_no_bonus_rewards(): void
    {
        $notification = new DonationAppliedNotification(
            amount: 25.00,
            totalUbers: 500,
            newBalance: 1500,
            paymentMethod: 'paypal',
        );
        $user = User::factory()->make();

        $mailMessage = $notification->toMail($user);

        $this->assertStringContainsString('/donate', $mailMessage->viewData['shopUrl']);
    }

    #[Test]
    public function it_includes_claim_url_in_view_data(): void
    {
        $notification = new DonationAppliedNotification(
            amount: 25.00,
            totalUbers: 500,
            newBalance: 1500,
            paymentMethod: 'paypal',
            bonusRewards: [
                'xilero' => [
                    ['item_name' => 'Test Item', 'item_id' => 123, 'quantity' => 1, 'refine_level' => 0, 'icon_url' => 'http://example.com/icon.png'],
                ],
                'xileretro' => [],
            ],
        );
        $user = User::factory()->make();

        $mailMessage = $notification->toMail($user);

        $this->assertStringContainsString('/dashboard', $mailMessage->viewData['claimUrl']);
    }

    #[Test]
    public function it_stores_donation_details(): void
    {
        $notification = new DonationAppliedNotification(
            amount: 25.00,
            totalUbers: 500,
            newBalance: 1500,
            paymentMethod: 'paypal',
        );

        $this->assertEquals(25.00, $notification->amount);
        $this->assertEquals(500, $notification->totalUbers);
        $this->assertEquals(1500, $notification->newBalance);
        $this->assertEquals('paypal', $notification->paymentMethod);
    }

    #[Test]
    public function it_converts_to_array(): void
    {
        $notification = new DonationAppliedNotification(
            amount: 25.00,
            totalUbers: 500,
            newBalance: 1500,
            paymentMethod: 'paypal',
        );
        $user = User::factory()->make();

        $array = $notification->toArray($user);

        $this->assertEquals(25.00, $array['amount']);
        $this->assertEquals(500, $array['total_ubers']);
        $this->assertEquals(1500, $array['new_balance']);
        $this->assertEquals('paypal', $array['payment_method']);
    }

    #[Test]
    public function it_uses_markdown_view(): void
    {
        $notification = new DonationAppliedNotification(
            amount: 25.00,
            totalUbers: 500,
            newBalance: 1500,
            paymentMethod: 'paypal',
        );
        $user = User::factory()->make();

        $mailMessage = $notification->toMail($user);

        $this->assertEquals('emails.donation-applied', $mailMessage->markdown);
    }

    #[Test]
    public function it_includes_bonus_rewards_in_view_data(): void
    {
        $bonusRewards = [
            'xilero' => [
                ['item_name' => 'XileRO Item', 'item_id' => 123, 'quantity' => 2, 'refine_level' => 5, 'icon_url' => 'http://example.com/icon1.png'],
            ],
            'xileretro' => [
                ['item_name' => 'XileRetro Item', 'item_id' => 456, 'quantity' => 1, 'refine_level' => 0, 'icon_url' => 'http://example.com/icon2.png'],
            ],
        ];

        $notification = new DonationAppliedNotification(
            amount: 50.00,
            totalUbers: 1000,
            newBalance: 2000,
            paymentMethod: 'paypal',
            bonusRewards: $bonusRewards,
        );
        $user = User::factory()->make();

        $mailMessage = $notification->toMail($user);

        $this->assertEquals($bonusRewards, $mailMessage->viewData['bonusRewards']);
    }
}
