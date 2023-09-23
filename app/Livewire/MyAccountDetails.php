<?php

namespace App\Livewire;

use Livewire\Component;

class MyAccountDetails extends Component
{
    public function characters()
    {
        return \App\Ragnarok\Login::find(auth()->user()->userLogins()->pluck('login_account_id')->first())->chars;
    }

    public function render()
    {
        return view('livewire.my-account-details');
    }
}
