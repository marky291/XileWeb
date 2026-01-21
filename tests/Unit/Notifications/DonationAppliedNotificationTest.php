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
    public function it_includes_donation_amount(): void
    {
        $notification = new DonationAppliedNotification(
            amount: 25.00,
            totalUbers: 500,
            newBalance: 1500,
            paymentMethod: 'paypal',
        );
        $user = User::factory()->make();

        $mailMessage = $notification->toMail($user);

        $introText = implode(' ', $mailMessage->introLines);
        $this->assertStringContainsString('25', $introText);
    }

    #[Test]
    public function it_includes_ubers_received(): void
    {
        $notification = new DonationAppliedNotification(
            amount: 25.00,
            totalUbers: 500,
            newBalance: 1500,
            paymentMethod: 'paypal',
        );
        $user = User::factory()->make();

        $mailMessage = $notification->toMail($user);

        $introText = implode(' ', $mailMessage->introLines);
        $this->assertStringContainsString('500', $introText);
    }

    #[Test]
    public function it_includes_new_balance(): void
    {
        $notification = new DonationAppliedNotification(
            amount: 25.00,
            totalUbers: 500,
            newBalance: 1500,
            paymentMethod: 'paypal',
        );
        $user = User::factory()->make();

        $mailMessage = $notification->toMail($user);

        $introText = implode(' ', $mailMessage->introLines);
        $this->assertStringContainsString('1500', $introText);
    }

    #[Test]
    public function it_includes_user_name_in_greeting(): void
    {
        $notification = new DonationAppliedNotification(
            amount: 25.00,
            totalUbers: 500,
            newBalance: 1500,
            paymentMethod: 'paypal',
        );
        $user = User::factory()->make(['name' => 'TestDonor']);

        $mailMessage = $notification->toMail($user);

        $this->assertStringContainsString('TestDonor', $mailMessage->greeting);
    }

    #[Test]
    public function it_includes_uber_shop_action(): void
    {
        $notification = new DonationAppliedNotification(
            amount: 25.00,
            totalUbers: 500,
            newBalance: 1500,
            paymentMethod: 'paypal',
        );
        $user = User::factory()->make();

        $mailMessage = $notification->toMail($user);

        $this->assertEquals('Visit the Uber Shop', $mailMessage->actionText);
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
}
