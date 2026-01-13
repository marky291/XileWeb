<?php

namespace App\Providers;

use App\Auth\RagnarokUserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

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
        Auth::provider('ragnarok', function ($app, array $config) {
            return new RagnarokUserProvider($app['hash'], $config['model']);
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
