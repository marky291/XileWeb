<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
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
        $this->ensureIsNotRateLimited();
        $this->validate();

        $user = User::create([
            'email' => $this->email,
            'password' => $this->password,
            'registration_ip' => request()->ip(),
        ]);

        RateLimiter::clear($this->throttleKey());

        event(new Registered($user));

        Auth::login($user);
        session()->regenerate();

        $this->redirect(route('verification.notice'), navigate: false);
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
            'email' => __('Too many registration attempts. Please try again in :minutes minutes.', [
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    protected function throttleKey(): string
    {
        return 'register:'.request()->ip();
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
