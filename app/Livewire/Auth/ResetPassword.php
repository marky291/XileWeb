<?php

namespace App\Livewire\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('layouts.app')]
class ResetPassword extends Component
{
    #[Locked]
    public string $token = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

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

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = request()->query('email', '');
    }

    public function rules(): array
    {
        return [
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', PasswordRule::default()],
        ];
    }

    public function resetPassword(): void
    {
        $this->validate();

        $resetUser = null;

        $status = Password::reset(
            [
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token' => $this->token,
            ],
            function ($user, $password) use (&$resetUser) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));

                $resetUser = $user;
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            Auth::login($resetUser);
            $this->redirect(route('dashboard'), navigate: false);
        } else {
            $this->addError('email', __($status));
        }
    }

    public function render()
    {
        return view('livewire.auth.reset-password');
    }
}
