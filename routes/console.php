<?php

use App\WoeEvents\WoeEventScheduleJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('horizon:snapshot', function () {
    $this->call('horizon:snapshot');
})->purpose('Take Horizon snapshot')->everyFiveMinutes();

// Job scheduling moved to app/Console/Kernel.php in Laravel 11