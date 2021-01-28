<?php

namespace App\Http\Livewire;

use App\Http\Controllers\Auth\LoginController;
use App\Ragnarok\Login;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Register extends Component
{

    public $username, $email, $password, $password_confirmation;

    public $registrationComplete = false;

    public $error;

    /**
     * Register for a new account.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function register()
    {
        $this->validate([
            'username' => ['required', 'string','unique:main.login,userid', 'min:4', 'max:23'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:main.login'],
            'password' => ['required', 'string', 'min:6', 'max:31', 'confirmed'],
        ]);

        $account = Login::create([
            'userid' => $this->username,
            'email' => $this->email,
            'user_pass' => hash('sha256', $this->password . config('database.secret')),
        ]);

        if ($account->account_id == null){
            $this->error = "Unable to create account, please report to GM or Admins.";
            return;
        }

        Auth::loginUsingId($account->account_id);

        session()->flash('message', 'Your new account is now ready!.');

        $this->registrationComplete = true;
    }

    public function render()
    {
        return view('livewire.register');
    }
}
