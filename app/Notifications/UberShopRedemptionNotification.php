<?php

namespace App\Notifications;

use App\Models\UberShopPurchase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UberShopRedemptionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public UberShopPurchase $purchase,
        public string $serverName,
        public int $refundHours = 24,
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
        $characterName = $this->purchase->claimed_by_char_name ?? 'your character';
        $refundDeadline = $this->purchase->claimed_at?->addHours($this->refundHours)->format('M j, Y \a\t g:i A') ?? 'N/A';

        return (new MailMessage)
            ->subject("Item Claimed - {$displayName}")
            ->greeting("Hey {$notifiable->name}!")
            ->line('Your item has been successfully delivered to your character!')
            ->line("**Item:** {$displayName}")
            ->line("**Quantity:** {$this->purchase->quantity}")
            ->line("**Server:** {$this->serverName}")
            ->line("**Claimed By:** {$characterName}")
            ->line('**Claimed At:** '.($this->purchase->claimed_at?->format('M j, Y \a\t g:i A') ?? 'Just now'))
            ->line('---')
            ->line('**Need a Refund?**')
            ->line("We understand that sometimes things don't work out as expected. You can request a refund within **{$this->refundHours} hours** of claiming your item.")
            ->line("**Refund Deadline:** {$refundDeadline}")
            ->line('**How to request a refund:**')
            ->line('1. Visit the Uber Shop page on our website')
            ->line('2. Find your purchase in the "Recent Purchases" section')
            ->line('3. Click the "Refund" button next to the item')
            ->line('**Important:** The item must still be in your inventory (unmodified, not traded, not dropped) for the refund to process.')
            ->action('View Uber Shop', url('/donate-shop'))
            ->line('---')
            ->line('Enjoy your new item and happy gaming!')
            ->salutation('See you in-game! - The XileRO Team');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'purchase_id' => $this->purchase->id,
            'item_name' => $this->purchase->item_name,
            'claimed_by' => $this->purchase->claimed_by_char_name,
            'server' => $this->serverName,
        ];
    }
}
