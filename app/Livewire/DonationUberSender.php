<?php

namespace App\Livewire;

use App\Ragnarok\DonationUber;
use App\Ragnarok\Login;
use Illuminate\Validation\Rules\Exists;
use Livewire\Attributes\Rule;
use Livewire\Component;

class DonationUberSender extends Component
{
    #[Rule(['required', 'alpha_num', 'min:4', 'max:23', new Exists('main.login', 'userid')])]
    public $username = '';

    #[Rule(['required', 'integer'])]
    public $uber_amount = '';

    public DonationUber|null $donation_uber_information = null;

    public bool $isSent = false;

    public function send()
    {
        $this->validate();

        $login = Login::firstWhere('userid', '=', $this->username);

        $donation_uber_information = DonationUber::firstOrCreate([
            'account_id' => $login->account_id
        ], [
            'pending_ubers' => 0,
            'username' => $this->username,
        ]);

        $donation_uber_information->increment('pending_ubers', $this->uber_amount);

        $donation_uber_information->save();

        $this->isSent = true;
    }

    public function render()
    {
        return view('livewire.donation-uber-sender');
    }
}
