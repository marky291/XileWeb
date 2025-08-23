<?php

namespace App\Livewire;

use App\Ragnarok\Login;
use Livewire\Component;

class MyAccountDetails extends Component
{
    public function characters()
    {
        $loginAccountId = auth()->user()->userLogins()->pluck('login_account_id')->first();
        
        if (!$loginAccountId) {
            return collect(); // Return empty collection if no login account
        }
        
        $login = Login::find($loginAccountId);
        
        if (!$login) {
            return collect(); // Return empty collection if login not found
        }
        
        return $login->chars;
    }

    public function render()
    {
        return view('livewire.my-account-details');
    }
}
