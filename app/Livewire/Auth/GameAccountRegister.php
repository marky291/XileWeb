<?php

namespace App\Livewire\Auth;

use App\Actions\MakeHashedLoginPassword;
use App\XileRO\XileRO_Login;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class GameAccountRegister extends Component
{
    public bool $embedded = false;

    public string $username = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function rules(): array
    {
        return [
            'username' => [
                'required',
                'string',
                'alpha_num',
                'min:4',
                'max:23',
                'unique:xilero_main.login,userid',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:39',
                'unique:xilero_main.login,email',
                'not_in:a@a.com',
            ],
            'password' => [
                'required',
                'string',
                'min:6',
                'max:31',
                'confirmed',
                Password::default(),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'username.unique' => 'This username is already taken.',
            'username.alpha_num' => 'Username must contain only letters and numbers.',
            'email.unique' => 'This email is already registered.',
            'email.not_in' => 'This email address is not allowed.',
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }

    public function register(): void
    {
        $this->validate();

        $login = XileRO_Login::create([
            'userid' => $this->username,
            'email' => $this->email,
            'user_pass' => MakeHashedLoginPassword::run($this->password),
        ]);

        Auth::login($login);

        session()->regenerate();

        $this->redirect(route('dashboard'), navigate: false);
    }

    public function render()
    {
        return view('livewire.auth.game-account-register', [
            'embedded' => $this->embedded,
        ]);
    }
}
