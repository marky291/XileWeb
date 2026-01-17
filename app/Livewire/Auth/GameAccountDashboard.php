<?php

namespace App\Livewire\Auth;

use App\Actions\ResetCharacterPosition;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class GameAccountDashboard extends Component
{
    public ?int $selectedCharacterId = null;

    /**
     * Get all characters for the authenticated user.
     */
    public function characters()
    {
        return auth()->user()->chars()->with('guild')->get();
    }

    /**
     * Select a character to view details.
     */
    public function selectCharacter(?int $charId): void
    {
        $this->selectedCharacterId = $charId;
    }

    /**
     * Get the currently selected character with full details.
     */
    public function selectedCharacter(): ?Char
    {
        if (! $this->selectedCharacterId) {
            return null;
        }

        $character = Char::with('guild')->find($this->selectedCharacterId);

        if (! $character) {
            return null;
        }

        // Verify the character belongs to the authenticated user
        if ($character->account_id !== auth()->user()->account_id) {
            return null;
        }

        return $character;
    }

    /**
     * Reset a character's position to the default save point.
     * Only works for offline characters.
     */
    public function resetPosition(int $charId): void
    {
        $character = Char::find($charId);

        if (! $character) {
            session()->flash('error', 'Character not found.');

            return;
        }

        // Verify the character belongs to the authenticated user
        if ($character->account_id !== auth()->user()->account_id) {
            session()->flash('error', 'You do not own this character.');

            return;
        }

        // Check if character is online
        if ($character->online) {
            session()->flash('error', 'Cannot reset position for an online character. Please log out first.');

            return;
        }

        ResetCharacterPosition::run($character);

        session()->flash('success', "{$character->name}'s position has been reset to Prontera.");
    }

    public function render()
    {
        return view('livewire.auth.game-account-dashboard', [
            'characters' => $this->characters(),
            'selectedChar' => $this->selectedCharacter(),
        ]);
    }
}
