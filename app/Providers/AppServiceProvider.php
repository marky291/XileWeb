<?php

namespace App\Providers;

use App\Auth\RagnarokUserProvider;
use App\Models\UberShopPurchase;
use App\Observers\UberShopPurchaseObserver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use SocialiteProviders\Discord\Provider as DiscordProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configure password validation defaults for security
        Password::defaults(function () {
            $rule = Password::min(8);

            return $this->app->isProduction()
                ? $rule->letters()->mixedCase()->numbers()->uncompromised()
                : $rule;
        });

        Auth::provider('ragnarok', function ($app, array $config) {
            return new RagnarokUserProvider($app['hash'], $config['model']);
        });

        // Register model observers
        UberShopPurchase::observe(UberShopPurchaseObserver::class);

        // Register Discord Socialite provider
        Event::listen(function (SocialiteWasCalled $event) {
            $event->extendSocialite('discord', DiscordProvider::class);
        });

        if (config('app.env') === 'production') {
            $path = request()->path();

            // Force HTTPS except for patch routes
            if (! str_starts_with($path, 'xilero/patch/') && ! str_starts_with($path, 'retro/patch/')) {
                \Illuminate\Support\Facades\URL::forceScheme('https');
            }
        }
    }
}
