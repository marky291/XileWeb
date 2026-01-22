<?php

namespace App\Livewire\Auth;

use App\Actions\SyncGameAccountData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
class GameAccountLogin extends Component
{
    #[Validate('required|string')]
    public mixed $email = '';

    #[Validate('required|string')]
    public mixed $password = '';

    public mixed $remember = false;

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
     * Sanitize remember input to prevent array injection attacks.
     */
    public function updatingRemember(mixed &$value): void
    {
        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
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

    public function authenticate(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        session()->regenerate();

        // Update last login info
        Auth::user()->update([
            'last_login_ip' => request()->ip(),
            'last_login_at' => now(),
        ]);

        // Sync game account data from game database
        SyncGameAccountData::run(Auth::user());

        // Redirect to intended URL or dashboard
        $intendedUrl = session()->pull('url.intended', route('dashboard'));
        $this->redirect($intendedUrl, navigate: false);
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(): string
    {
        return strtolower($this->email).'|'.request()->ip();
    }

    public function render()
    {
        return view('livewire.auth.game-account-login');
    }
}
