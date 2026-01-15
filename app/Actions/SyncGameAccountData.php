<?php

namespace App\Actions;

use App\Models\SyncedCharacter;
use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class SyncGameAccountData
{
    use AsAction;

    public function handle(User $user): int
    {
        $syncedCount = 0;

        foreach ($user->gameAccounts as $gameAccount) {
            if (! $gameAccount->ragnarok_account_id) {
                continue;
            }

            // Sync security code status
            $gameAccount->update([
                'has_security_code' => $gameAccount->hasSecurityCode(),
            ]);

            // Get characters from game database
            $characters = $gameAccount->chars()->with('guild')->get();

            // Get existing synced char_ids to detect deleted characters
            $existingCharIds = $gameAccount->syncedCharacters()->pluck('char_id')->toArray();
            $currentCharIds = [];

            foreach ($characters as $character) {
                $currentCharIds[] = $character->char_id;

                SyncedCharacter::updateOrCreate(
                    [
                        'game_account_id' => $gameAccount->id,
                        'char_id' => $character->char_id,
                    ],
                    [
                        'name' => $character->name,
                        'class_name' => $character->class_name,
                        'base_level' => $character->base_level,
                        'job_level' => $character->job_level,
                        'zeny' => $character->zeny,
                        'last_map' => $character->last_map,
                        'guild_name' => $character->guild?->name,
                        'online' => (bool) $character->online,
                        'synced_at' => now(),
                    ]
                );

                $syncedCount++;
            }

            // Remove characters that no longer exist in game DB
            $deletedCharIds = array_diff($existingCharIds, $currentCharIds);
            if (! empty($deletedCharIds)) {
                $gameAccount->syncedCharacters()
                    ->whereIn('char_id', $deletedCharIds)
                    ->delete();
            }
        }

        return $syncedCount;
    }
}
