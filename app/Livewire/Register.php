<?php

namespace App\Livewire;

use App\Models\User;
use App\Ragnarok\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Reactive;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Register extends Component
{
    #[Rule(['required', 'alpha_num', 'unique:main.login,userid', 'min:4', 'max:23'])]
    public $username = '';

    #[Rule(['required', 'string', 'email', 'max:39', 'unique:main.login'])]
    public $email;

    #[Rule(['required', 'string', 'min:6', 'max:31', 'confirmed'])]
    public $password;
    public $password_confirmation;

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
        $this->validate();

        $account = Login::create([
            'userid' => $this->username,
            'email' => $this->email,
            'user_pass' => hash('sha256', $this->password.config('database.secret')),
        ]);

        if ($account->account_id == null) {
            $this->error = 'Unable to create account, please report to GM or Admins.';
            return;
        }

        $user = User::create([
            'name' => $this->username,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        session()->flash('message', 'Your new account is now ready!.');

        $this->registrationComplete = true;
    }

    public function render()
    {
        return view('livewire.register');
    }
}
