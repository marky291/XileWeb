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
        //$schedule->command('inspire')->everyFiveSeconds();
        $schedule->command('horizon:snapshot')->everyFiveMinutes();

        // schedule woe point calculations
        $schedule->job(new PostGuildPointsToDiscordJob('Kriemhild', today()))->hourly()->name('Kriemhild Points')->withoutOverlapping();
        $schedule->job(new PostGuildPointsToDiscordJob('Swanhild', today()))->hourly()->name('Swanhild Points')->withoutOverlapping();
        // $schedule->job(new PostGuildPointsToDiscordJob('Fadhringh', today()))->hourly()->name('Fadhringh Points')->withoutOverlapping();
        // $schedule->job(new PostGuildPointsToDiscordJob('Skoegul', today()))->hourly()->name('Skoegul Points')->withoutOverlapping();
        // $schedule->job(new PostGuildPointsToDiscordJob('Gondul', today()))->hourly()->name('Gondul Points')->withoutOverlapping();
        $schedule->job(new PostGuildPointsToDiscordJob('Hljod', today()))->hourly()->name('Hljod Points')->withoutOverlapping();
        $schedule->job(new PostGuildPointsToDiscordJob('Cyr', today()))->hourly()->name('Cyr Points')->withoutOverlapping();
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
