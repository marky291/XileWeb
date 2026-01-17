<?php

namespace App\Livewire\Auth;

use App\Actions\MakeHashedLoginPassword;
use App\XileRetro\XileRetro_Login;
use App\XileRO\XileRO_Login;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class GameAccountRegister extends Component
{
    public bool $embedded = false;

    public string $server = 'xilero';

    public string $username = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function rules(): array
    {
        $database = $this->server === 'xileretro' ? 'xileretro_main' : 'xilero_main';

        return [
            'server' => [
                'required',
                'in:xilero,xileretro',
            ],
            'username' => [
                'required',
                'string',
                'alpha_num',
                'min:4',
                'max:23',
                "unique:{$database}.login,userid",
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:39',
                "unique:{$database}.login,email",
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
            'username.unique' => 'This username is already taken on this server.',
            'username.alpha_num' => 'Username must contain only letters and numbers.',
            'email.unique' => 'This email is already registered on this server.',
            'email.not_in' => 'This email address is not allowed.',
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }

    public function register(): void
    {
        $this->validate();

        $loginData = [
            'userid' => $this->username,
            'email' => $this->email,
            'user_pass' => MakeHashedLoginPassword::run($this->password),
        ];

        if ($this->server === 'xileretro') {
            $login = XileRetro_Login::create($loginData);
        } else {
            $login = XileRO_Login::create($loginData);
        }

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
