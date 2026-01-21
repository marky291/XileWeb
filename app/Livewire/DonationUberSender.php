<?php

namespace App\Livewire;

use App\Models\GameAccount;
use Illuminate\Validation\Rules\Exists;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

class DonationUberSender extends Component
{
    #[Rule(['required', 'alpha_num', 'min:4', 'max:23', new Exists('game_accounts', 'userid')])]
    public string $username = '';

    #[Rule(['required', 'integer', 'min:1'])]
    public int $uber_amount = 0;

    public bool $isSent = false;

    /**
     * Authorize admin access on component mount.
     */
    public function mount(): void
    {
        $this->authorizeAdmin();
    }

    /**
     * Ensure only authenticated admins can access this component.
     */
    protected function authorizeAdmin(): void
    {
        if (! auth()->check() || ! auth()->user()->isAdmin()) {
            abort(Response::HTTP_FORBIDDEN, 'Unauthorized access.');
        }
    }

    /**
     * Sanitize username input to prevent array injection attacks.
     */
    public function updatingUsername(mixed &$value): void
    {
        $value = is_string($value) ? $value : '';
    }

    /**
     * Sanitize uber_amount input to prevent array injection attacks.
     */
    public function updatingUberAmount(mixed &$value): void
    {
        $value = is_numeric($value) ? (int) $value : 0;
    }

    public function send(): void
    {
        $this->authorizeAdmin();
        $this->validate();

        $gameAccount = GameAccount::firstWhere('userid', '=', $this->username);

        if (! $gameAccount) {
            session()->flash('error', 'Game account not found.');
            return;
        }

        $gameAccount->increment('uber_balance', $this->uber_amount);

        $this->isSent = true;
        session()->flash('success', "Successfully sent {$this->uber_amount} Ubers to {$this->username}.");
    }

    public function render()
    {
        return view('livewire.donation-uber-sender');
    }
}
