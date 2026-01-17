<?php

namespace App\Livewire;

use App\Models\GameAccount;
use Illuminate\Validation\Rules\Exists;
use Livewire\Attributes\Rule;
use Livewire\Component;

class DonationUberSender extends Component
{
    #[Rule(['required', 'alpha_num', 'min:4', 'max:23', new Exists('game_accounts', 'userid')])]
    public $username = '';

    #[Rule(['required', 'integer'])]
    public $uber_amount = '';

    public bool $isSent = false;

    public function send()
    {
        $this->validate();

        $gameAccount = GameAccount::firstWhere('userid', '=', $this->username);

        $gameAccount->increment('uber_balance', $this->uber_amount);

        $this->isSent = true;
    }

    public function render()
    {
        return view('livewire.donation-uber-sender');
    }
}
