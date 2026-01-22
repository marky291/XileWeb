<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class VerifyEmail extends Component
{
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirect(route('dashboard'), navigate: false);

            return;
        }

        $throttleKey = 'verify-email:'.$user->id;

        if (RateLimiter::tooManyAttempts($throttleKey, 1)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            session()->flash('throttle', $seconds);

            return;
        }

        RateLimiter::hit($throttleKey, 60);

        $user->sendEmailVerificationNotification();

        session()->flash('status', 'verification-link-sent');
    }

    public function render()
    {
        return view('livewire.auth.verify-email');
    }
}
