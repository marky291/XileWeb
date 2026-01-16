<?php

namespace App\Actions;

use App\Models\GameAccount;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class TransferLegacyUberBalance
{
    use AsAction;

    /**
     * Transfer legacy uber balance from a game account to a master account.
     * Clears the legacy_uber_balance on the game account after transfer.
     *
     * @return int The amount of ubers transferred
     */
    public function handle(GameAccount $gameAccount, User $user): int
    {
        if ($gameAccount->legacy_uber_balance <= 0) {
            return 0;
        }

        return DB::transaction(function () use ($gameAccount, $user) {
            $amount = $gameAccount->legacy_uber_balance;

            $user->increment('uber_balance', $amount);

            $gameAccount->update([
                'legacy_uber_balance' => 0,
            ]);

            return $amount;
        });
    }
}
