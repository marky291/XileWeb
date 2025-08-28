<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Laravel\Pennant\Feature;

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
        if (config('app.env') === 'production') {
            $path = request()->path();
            
            // Force HTTPS except for patch routes
            if (!str_starts_with($path, 'xilero/patch/') && !str_starts_with($path, 'retro/patch/')) {
                \Illuminate\Support\Facades\URL::forceScheme('https');
            }
        }
    }
}
