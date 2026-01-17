<?php

namespace App\Notifications;

use App\Models\UberShopPurchase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UberShopPurchaseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public UberShopPurchase $purchase,
        public string $gameAccountName,
        public string $serverName,
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
        $itemName = $this->purchase->item_name;
        $refine = $this->purchase->refine_level > 0 ? "+{$this->purchase->refine_level} " : '';
        $displayName = $refine.$itemName;

        return (new MailMessage)
            ->subject("Purchase Confirmed - {$displayName}")
            ->greeting("Hey {$notifiable->name}!")
            ->line('Great news! Your purchase from the Uber Shop has been confirmed.')
            ->line("**Item:** {$displayName}")
            ->line("**Quantity:** {$this->purchase->quantity}")
            ->line("**Cost:** {$this->purchase->uber_cost} Ubers")
            ->line("**Remaining Balance:** {$this->purchase->uber_balance_after} Ubers")
            ->line("**Server:** {$this->serverName}")
            ->line("**Delivery To:** {$this->gameAccountName}")
            ->line('---')
            ->line('**How to claim your item:**')
            ->line("1. Log into **{$this->serverName}** with your account: **{$this->gameAccountName}**")
            ->line('2. Your item will be delivered automatically when you log in')
            ->line('3. Check your inventory for the item')
            ->line('---')
            ->line('Changed your mind? You can cancel this purchase from the Uber Shop page before claiming it in-game for a full refund.')
            ->action('View Uber Shop', url('/donate-shop'))
            ->salutation('Happy shopping! - The XileRO Team');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'purchase_id' => $this->purchase->id,
            'item_name' => $this->purchase->item_name,
            'uber_cost' => $this->purchase->uber_cost,
            'server' => $this->serverName,
        ];
    }
}
