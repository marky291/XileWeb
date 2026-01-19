<?php

namespace App\Actions;

use App\Models\GameAccount;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class ResetGameAccountPassword
{
    use AsAction;

    public function handle(GameAccount $gameAccount, string $newPassword): void
    {
        $hashedPassword = MakeHashedLoginPassword::run($newPassword, $gameAccount->server);

        DB::transaction(function () use ($gameAccount, $hashedPassword) {
            // Update the game database login record
            $login = $gameAccount->ragnarokLogin();

            if ($login) {
                $login->update(['user_pass' => $hashedPassword]);
            }

            // Update the local game account record
            $gameAccount->update(['user_pass' => $hashedPassword]);
        });
    }
}
