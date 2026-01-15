<?php

namespace App\Livewire\Auth;

use App\Models\User;
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

        Auth::login($user);
        session()->regenerate();

        $this->redirect(route('dashboard'), navigate: false);
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
