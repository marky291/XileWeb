<?php

namespace App\Console;

use App\Actions\ProcessWoeEventPoints;
use App\Jobs\GuildMessagesToDiscord;
use App\Jobs\PostGuildPointsToDiscordJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        //$schedule->command('inspire')->hourly();
        $schedule->command('horizon:snapshot')->everyFiveMinutes();
        $schedule->job(new PostGuildPointsToDiscordJob('Kriemhild', today()))->dailyAt(config('xilero.woe_events.send_discord'))->withoutOverlapping();
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
