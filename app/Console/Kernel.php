<?php

namespace App\Console;

use App\WoeEvents\WoeEventScheduleJob;
use App\WoeEvents\WoeEventDiscordChannelResolver;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        //$schedule->command('inspire')->everyFiveSeconds();
        $schedule->command('horizon:snapshot')->everyFiveMinutes();

        $castles = [
            'Kriemhild',
            'Swanhild',
            // 'Fadhringh',
            // 'Skoegul',
            // 'Gondul',
            'Hljod',
            'Cyr',
        ];

        // schedule woe event messages
        foreach ($castles as $castle) {
            $schedule->job(new WoeEventScheduleJob())->hourly()->name("{$castle} Points")->withoutOverlapping();
        }
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
