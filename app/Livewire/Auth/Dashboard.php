<?php

namespace App\Livewire\Auth;

use App\Actions\CreateGameAccount;
use App\Actions\ResetCharacterPosition;
use App\Actions\SyncGameAccountData;
use App\Models\GameAccount;
use App\Models\SyncedCharacter;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public bool $showCreateForm = false;

    public string $gameServer = 'xilero';

    public string $gameUsername = '';

    public string $gamePassword = '';

    public string $gamePassword_confirmation = '';

    public ?int $selectedGameAccountId = null;

    public ?int $selectedCharacterId = null;

    public function rules(): array
    {
        $loginTable = $this->gameServer === 'xileretro' ? 'xileretro_main.login' : 'xilero_main.login';

        return [
            'gameServer' => [
                'required',
                'string',
                'in:xilero,xileretro',
            ],
            'gameUsername' => [
                'required',
                'string',
                'alpha_num',
                'min:4',
                'max:23',
                "unique:game_accounts,userid,NULL,id,server,{$this->gameServer}",
                "unique:{$loginTable},userid",
            ],
            'gamePassword' => [
                'required',
                'string',
                'min:6',
                'max:31',
                'confirmed',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'gameUsername.unique' => 'This username is already taken on this server.',
            'gameUsername.alpha_num' => 'Username must contain only letters and numbers.',
            'gamePassword.confirmed' => 'The password confirmation does not match.',
        ];
    }

    public function createGameAccount(): void
    {
        if (! auth()->user()->canCreateGameAccount()) {
            session()->flash('error', 'You have reached the maximum number of game accounts.');

            return;
        }

        $this->validate();

        CreateGameAccount::run(auth()->user(), [
            'server' => $this->gameServer,
            'userid' => $this->gameUsername,
            'email' => auth()->user()->email,
            'password' => $this->gamePassword,
            'sex' => 'M',
        ]);

        $this->reset(['gameServer', 'gameUsername', 'gamePassword', 'gamePassword_confirmation']);
        $this->gameServer = 'xilero';
        $this->showCreateForm = false;

        // Sync the new account's data
        SyncGameAccountData::run(auth()->user());

        session()->flash('success', 'Game account created successfully!');
    }

    public const REFRESH_COOLDOWN_SECONDS = 60;

    public function refreshData(): void
    {
        $sessionKey = 'last_game_data_refresh';
        $lastRefresh = session($sessionKey);
        $now = time();

        // Handle old Carbon values in session
        if ($lastRefresh && ! is_int($lastRefresh)) {
            $lastRefresh = null;
            session()->forget($sessionKey);
        }

        if ($lastRefresh && ($now - $lastRefresh) < self::REFRESH_COOLDOWN_SECONDS) {
            $remaining = self::REFRESH_COOLDOWN_SECONDS - ($now - $lastRefresh);
            session()->flash('error', "Please wait {$remaining} seconds before refreshing again.");

            return;
        }

        session([$sessionKey => $now]);

        $count = SyncGameAccountData::run(auth()->user());
        session()->flash('success', "Synced {$count} characters from game server.");
    }

    public function selectGameAccount(?int $gameAccountId): void
    {
        $this->selectedGameAccountId = $gameAccountId;
        $this->selectedCharacterId = null;
    }

    public function selectCharacter(?int $charId): void
    {
        $this->selectedCharacterId = $this->selectedCharacterId === $charId ? null : $charId;
    }

    public function selectedGameAccount(): ?GameAccount
    {
        if (! $this->selectedGameAccountId) {
            return null;
        }

        return auth()->user()->gameAccounts()->find($this->selectedGameAccountId);
    }

    public function resetPosition(int $charId): void
    {
        // Find the synced character to get the game account
        $syncedChar = SyncedCharacter::where('char_id', $charId)
            ->whereHas('gameAccount', fn ($q) => $q->where('user_id', auth()->id()))
            ->first();

        if (! $syncedChar) {
            session()->flash('error', 'Character not found.');

            return;
        }

        if ($syncedChar->online) {
            session()->flash('error', 'Cannot reset position for an online character.');

            return;
        }

        // Get the actual character from game DB for the reset action
        $gameAccount = $syncedChar->gameAccount;
        $character = $gameAccount->chars()->find($charId);

        if (! $character) {
            session()->flash('error', 'Character not found in game database.');

            return;
        }

        ResetCharacterPosition::run($character);

        // Update local synced data
        $syncedChar->update(['last_map' => 'prontera']);

        session()->flash('success', "{$syncedChar->name}'s position has been reset.");
    }

    public function resetSecurity(int $gameAccountId): void
    {
        $gameAccount = auth()->user()->gameAccounts()->find($gameAccountId);

        if (! $gameAccount) {
            session()->flash('error', 'Game account not found.');

            return;
        }

        if (! $gameAccount->hasSecurityCode()) {
            session()->flash('error', 'No security code is set for this account.');

            return;
        }

        // Check if any characters are online
        if ($gameAccount->hasOnlineCharacters()) {
            session()->flash('error', 'Cannot reset security while logged in. Please log out of the game first.');

            return;
        }

        if ($gameAccount->resetSecurityCode()) {
            $gameAccount->update(['has_security_code' => false]);
            session()->flash('success', "@security has been reset for {$gameAccount->userid}. You can set a new one in-game.");
        } else {
            session()->flash('error', 'Failed to reset security code.');
        }
    }

    public function render()
    {
        $gameAccounts = auth()->user()->gameAccounts()
            ->with('syncedCharacters')
            ->get();

        return view('livewire.auth.dashboard', [
            'user' => auth()->user(),
            'gameAccounts' => $gameAccounts,
            'canCreateMore' => auth()->user()->canCreateGameAccount(),
        ]);
    }
}
