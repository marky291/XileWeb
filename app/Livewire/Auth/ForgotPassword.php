<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ForgotPassword extends Component
{
    public string $email = '';

    public bool $emailSent = false;

    /**
     * Sanitize email input to prevent array injection attacks.
     */
    public function updatingEmail(mixed &$value): void
    {
        $value = is_string($value) ? $value : '';
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
        ];
    }

    public function sendResetLink(): void
    {
        $this->validate();

        $status = Password::sendResetLink(['email' => $this->email]);

        if ($status === Password::RESET_LINK_SENT) {
            $this->emailSent = true;
            session()->flash('status', __($status));
        } elseif ($status === Password::RESET_THROTTLED) {
            $this->addError('email', 'Please wait 60 seconds before requesting another reset link.');
        } else {
            $this->addError('email', __($status));
        }
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
