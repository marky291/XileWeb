<?php

namespace Tests\Unit\Jobs;

use App\Jobs\SendDonationAppliedEmail;
use App\Models\DonationLog;
use App\Models\User;
use App\Notifications\DonationAppliedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SendDonationAppliedEmailTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_implements_should_queue(): void
    {
        $user = User::factory()->create();
        $donationLog = DonationLog::factory()->create(['user_id' => $user->id]);

        $job = new SendDonationAppliedEmail(
            donationLog: $donationLog,
            amount: 25.00,
            totalUbers: 500,
            newBalance: 1500,
            paymentMethod: 'paypal',
        );

        $this->assertInstanceOf(ShouldQueue::class, $job);
    }

    #[Test]
    public function it_sends_notification_to_user(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $donationLog = DonationLog::factory()->create([
            'user_id' => $user->id,
            'reverted_at' => null,
        ]);

        $job = new SendDonationAppliedEmail(
            donationLog: $donationLog,
            amount: 25.00,
            totalUbers: 500,
            newBalance: 1500,
            paymentMethod: 'paypal',
        );

        $job->handle();

        Notification::assertSentTo($user, DonationAppliedNotification::class);
    }

    #[Test]
    public function it_does_not_send_notification_if_donation_reverted(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $donationLog = DonationLog::factory()->create([
            'user_id' => $user->id,
            'reverted_at' => now(),
        ]);

        $job = new SendDonationAppliedEmail(
            donationLog: $donationLog,
            amount: 25.00,
            totalUbers: 500,
            newBalance: 1500,
            paymentMethod: 'paypal',
        );

        $job->handle();

        Notification::assertNotSentTo($user, DonationAppliedNotification::class);
    }

    #[Test]
    public function it_refreshes_donation_log_before_checking_reverted(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $donationLog = DonationLog::factory()->create([
            'user_id' => $user->id,
            'reverted_at' => null,
        ]);

        $job = new SendDonationAppliedEmail(
            donationLog: $donationLog,
            amount: 25.00,
            totalUbers: 500,
            newBalance: 1500,
            paymentMethod: 'paypal',
        );

        // Revert the donation AFTER creating the job
        $donationLog->update(['reverted_at' => now()]);

        $job->handle();

        // Should NOT send because job refreshes donation and sees it's reverted
        Notification::assertNotSentTo($user, DonationAppliedNotification::class);
    }

    #[Test]
    public function it_passes_correct_parameters_to_notification(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $donationLog = DonationLog::factory()->create([
            'user_id' => $user->id,
            'reverted_at' => null,
        ]);

        $job = new SendDonationAppliedEmail(
            donationLog: $donationLog,
            amount: 50.00,
            totalUbers: 1000,
            newBalance: 2500,
            paymentMethod: 'stripe',
        );

        $job->handle();

        Notification::assertSentTo($user, DonationAppliedNotification::class, function ($notification) {
            return $notification->amount === 50.00
                && $notification->totalUbers === 1000
                && $notification->newBalance === 2500
                && $notification->paymentMethod === 'stripe';
        });
    }

    #[Test]
    public function it_stores_job_parameters(): void
    {
        $user = User::factory()->create();
        $donationLog = DonationLog::factory()->create(['user_id' => $user->id]);

        $job = new SendDonationAppliedEmail(
            donationLog: $donationLog,
            amount: 25.00,
            totalUbers: 500,
            newBalance: 1500,
            paymentMethod: 'paypal',
        );

        $this->assertEquals($donationLog->id, $job->donationLog->id);
        $this->assertEquals(25.00, $job->amount);
        $this->assertEquals(500, $job->totalUbers);
        $this->assertEquals(1500, $job->newBalance);
        $this->assertEquals('paypal', $job->paymentMethod);
    }
}
