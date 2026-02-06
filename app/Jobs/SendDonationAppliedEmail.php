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
     * @param  array{xilero: array<int, array{item_name: string, item_id: int, quantity: int, refine_level: int, icon_url: string}>, xileretro: array<int, array{item_name: string, item_id: int, quantity: int, refine_level: int, icon_url: string}>}  $bonusRewards
     */
    public function __construct(
        public DonationLog $donationLog,
        public float $amount,
        public int $totalUbers,
        public int $newBalance,
        public string $paymentMethod,
        public array $bonusRewards = ['xilero' => [], 'xileretro' => []],
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
            bonusRewards: $this->bonusRewards,
        ));
    }
}
