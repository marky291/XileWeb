<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DonationAppliedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public float $amount,
        public int $totalUbers,
        public int $newBalance,
        public string $paymentMethod,
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
            ->greeting("Hey {$notifiable->name}!")
            ->line("Thank you so much for your generous donation of **\${$this->amount}**! Your support means the world to us and helps keep XileRO running strong.")
            ->line("We've added **{$this->totalUbers} Ubers** to your account.")
            ->line("**Your new Uber balance: {$this->newBalance} Ubers**")
            ->line("Payment received via: {$paymentMethodName}")
            ->action('Visit the Uber Shop', url('/donate'))
            ->line('Ready to spend your Ubers? Head over to the Uber Shop to browse exclusive gear, costumes, and items!')
            ->line('Thank you again for being an awesome part of our community. See you in-game!')
            ->salutation('With gratitude, The XileRO Team');
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
        ];
    }
}
