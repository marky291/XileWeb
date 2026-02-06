<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DonationAppliedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  array{xilero: array<int, array{item_name: string, item_id: int, quantity: int, refine_level: int, icon_url: string}>, xileretro: array<int, array{item_name: string, item_id: int, quantity: int, refine_level: int, icon_url: string}>}  $bonusRewards
     */
    public function __construct(
        public float $amount,
        public int $totalUbers,
        public int $newBalance,
        public string $paymentMethod,
        public array $bonusRewards = ['xilero' => [], 'xileretro' => []],
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $paymentMethodName = config("donation.payment_methods.{$this->paymentMethod}.name", $this->paymentMethod);

        return (new MailMessage)
            ->subject('Thank You for Your Donation!')
            ->markdown('emails.donation-applied', [
                'notifiable' => $notifiable,
                'amount' => $this->amount,
                'totalUbers' => $this->totalUbers,
                'newBalance' => $this->newBalance,
                'paymentMethodName' => $paymentMethodName,
                'bonusRewards' => $this->bonusRewards,
                'claimUrl' => url('/dashboard'),
                'shopUrl' => url('/donate'),
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'amount' => $this->amount,
            'total_ubers' => $this->totalUbers,
            'new_balance' => $this->newBalance,
            'payment_method' => $this->paymentMethod,
            'bonus_rewards' => $this->bonusRewards,
        ];
    }
}
