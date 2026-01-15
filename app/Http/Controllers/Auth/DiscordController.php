<?php

namespace App\Http\Controllers\Auth;

use App\Actions\SyncGameAccountData;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use SocialiteProviders\Discord\Provider;

class DiscordController extends Controller
{
    public function redirect(): RedirectResponse
    {
        /** @var Provider $driver */
        $driver = Socialite::driver('discord');

        return $driver
            ->scopes(['identify', 'email'])
            ->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            /** @var Provider $driver */
            $driver = Socialite::driver('discord');
            $discordUser = $driver->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Failed to authenticate with Discord. Please try again.');
        }

        // Scenario 1: User with this discord_id exists - log them in
        $user = User::where('discord_id', $discordUser->getId())->first();

        if ($user) {
            $this->updateDiscordData($user, $discordUser);
            Auth::login($user, remember: true);
            SyncGameAccountData::run($user);

            return redirect()->intended(route('dashboard'));
        }

        // Scenario 2: User with same email exists - link Discord to existing account
        if ($discordUser->getEmail()) {
            $user = User::where('email', $discordUser->getEmail())->first();

            if ($user) {
                $this->updateDiscordData($user, $discordUser);
                Auth::login($user, remember: true);
                SyncGameAccountData::run($user);

                return redirect()->intended(route('dashboard'));
            }
        }

        // Scenario 3: New user - create account (no game accounts to sync yet)
        $user = User::create([
            'name' => $discordUser->getName() ?? $discordUser->getNickname(),
            'email' => $discordUser->getEmail(),
            'password' => Str::random(32), // Random password for OAuth-only users
            'discord_id' => $discordUser->getId(),
            'discord_username' => $discordUser->getNickname(),
            'discord_avatar' => $discordUser->getAvatar(),
            'discord_token' => $discordUser->token,
            'discord_refresh_token' => $discordUser->refreshToken,
        ]);

        Auth::login($user, remember: true);

        return redirect()->route('dashboard');
    }

    private function updateDiscordData(User $user, $discordUser): void
    {
        $user->update([
            'discord_id' => $discordUser->getId(),
            'discord_username' => $discordUser->getNickname(),
            'discord_avatar' => $discordUser->getAvatar(),
            'discord_token' => $discordUser->token,
            'discord_refresh_token' => $discordUser->refreshToken,
        ]);
    }
}
