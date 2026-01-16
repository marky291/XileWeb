<?php

namespace App\Jobs;

use App\Models\DonationLog;
use App\Notifications\DonationAppliedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendDonationAppliedEmail implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public DonationLog $donationLog,
        public float $amount,
        public int $totalUbers,
        public int $newBalance,
        public string $paymentMethod,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Check if the donation was reverted before sending the email
        $this->donationLog->refresh();

        if ($this->donationLog->isReverted()) {
            // Donation was reverted, don't send the thank you email
            return;
        }

        // Send the thank you email
        $this->donationLog->user->notify(new DonationAppliedNotification(
            amount: $this->amount,
            totalUbers: $this->totalUbers,
            newBalance: $this->newBalance,
            paymentMethod: $this->paymentMethod,
        ));
    }
}
