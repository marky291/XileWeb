<?php

namespace App\Livewire\Auth;

use App\Actions\MakeHashedLoginPassword;
use App\XileRetro\XileRetro_Login;
use App\XileRO\XileRO_Login;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
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

    /**
     * Sanitize server input to prevent array injection attacks.
     */
    public function updatingServer(mixed &$value): void
    {
        $value = is_string($value) ? $value : 'xilero';
    }

    /**
     * Sanitize username input to prevent array injection attacks.
     */
    public function updatingUsername(mixed &$value): void
    {
        $value = is_string($value) ? $value : '';
    }

    /**
     * Sanitize email input to prevent array injection attacks.
     */
    public function updatingEmail(mixed &$value): void
    {
        $value = is_string($value) ? $value : '';
    }

    /**
     * Sanitize password input to prevent array injection attacks.
     */
    public function updatingPassword(mixed &$value): void
    {
        $value = is_string($value) ? $value : '';
    }

    /**
     * Sanitize password confirmation input to prevent array injection attacks.
     */
    public function updatingPasswordConfirmation(mixed &$value): void
    {
        $value = is_string($value) ? $value : '';
    }

    public function rules(): array
    {
        $loginModel = $this->server === 'xileretro' ? XileRetro_Login::class : XileRO_Login::class;

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
                "unique:{$loginModel},userid",
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:39',
                "unique:{$loginModel},email",
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
        $this->ensureIsNotRateLimited();
        $this->validate();

        $loginData = [
            'userid' => $this->username,
            'email' => $this->email,
            'user_pass' => MakeHashedLoginPassword::run($this->password, $this->server),
        ];

        if ($this->server === 'xileretro') {
            $login = XileRetro_Login::create($loginData);
        } else {
            $login = XileRO_Login::create($loginData);
        }

        RateLimiter::clear($this->throttleKey());

        Auth::login($login);

        session()->regenerate();

        $this->redirect(route('dashboard'), navigate: false);
    }

    /**
     * Ensure the registration request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            RateLimiter::hit($this->throttleKey(), 3600); // 1 hour decay

            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'username' => __('Too many registration attempts. Please try again in :minutes minutes.', [
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    protected function throttleKey(): string
    {
        return 'game-register:'.request()->ip();
    }

    public function render()
    {
        return view('livewire.auth.game-account-register', [
            'embedded' => $this->embedded,
        ]);
    }
}
