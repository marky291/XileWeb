<?php

namespace App\Observers;

use App\Models\GameAccount;
use App\Models\UberShopPurchase;
use App\Notifications\UberShopRedemptionNotification;

class UberShopPurchaseObserver
{
    /**
     * Handle the UberShopPurchase "updated" event.
     */
    public function updated(UberShopPurchase $purchase): void
    {
        // Check if status changed to claimed
        if ($purchase->isDirty('status') && $purchase->status === UberShopPurchase::STATUS_CLAIMED) {
            $this->sendRedemptionNotification($purchase);
        }
    }

    /**
     * Send redemption notification to the user.
     */
    protected function sendRedemptionNotification(UberShopPurchase $purchase): void
    {
        // Find the game account linked to this purchase
        $gameAccount = GameAccount::where('ragnarok_account_id', $purchase->account_id)->first();

        if (! $gameAccount) {
            return;
        }

        // Get the master account user
        $user = $gameAccount->user;

        if (! $user) {
            return;
        }

        // Send the redemption notification
        $user->notify(new UberShopRedemptionNotification(
            $purchase,
            $gameAccount->serverName(),
            24 // refund hours
        ));
    }
}
