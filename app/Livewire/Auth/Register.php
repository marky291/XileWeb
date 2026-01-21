<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Register extends Component
{
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

    public function mount(): void
    {
        // Store the referer as intended URL if not already set and it's from our site
        if (! session()->has('url.intended')) {
            $referer = request()->headers->get('referer');
            if ($referer && str_starts_with($referer, config('app.url'))) {
                // Don't store login/register pages as intended
                if (! str_contains($referer, '/login') && ! str_contains($referer, '/register')) {
                    session()->put('url.intended', $referer);
                }
            }
        }
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email', 'not_in:a@a.com'],
            'password' => ['required', 'string', 'min:8', 'confirmed', Password::default()],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'This email is already registered.',
            'email.not_in' => 'This email address is not allowed.',
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }

    public function register(): void
    {
        $this->validate();

        $user = User::create([
            'email' => $this->email,
            'password' => $this->password,
        ]);

        event(new Registered($user));

        Auth::login($user);
        session()->regenerate();

        $this->redirect(route('verification.notice'), navigate: false);
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
