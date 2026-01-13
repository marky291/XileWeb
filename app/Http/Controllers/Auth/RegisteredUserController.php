<?php

namespace App\Http\Controllers\Auth;

use App\Actions\MakeHashedLoginPassword;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Ragnarok\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'alpha_num', 'min:4', 'max:23', 'unique:main.login,userid'],
            'email' => ['required', 'string', 'email', 'max:39', 'unique:main.login,email'],
            'password' => ['required', 'confirmed', 'min:6', 'max:31', Password::defaults()],
        ]);

        $account = Login::create([
            'userid' => $request->name,
            'email' => $request->email,
            'user_pass' => MakeHashedLoginPassword::run($request->password),
        ]);

        event(new Registered($account));

        Auth::login($account);

        return redirect(RouteServiceProvider::HOME);
    }
}
