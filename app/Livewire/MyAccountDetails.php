<?php

namespace App\Livewire;

use App\Ragnarok\Login;
use Livewire\Component;

class MyAccountDetails extends Component
{
    public function characters()
    {
        return Login::find(auth()->user()->userLogins()->pluck('login_account_id')->first())->chars;
    }

    public function render()
    {
        return view('livewire.my-account-details');
    }
}
