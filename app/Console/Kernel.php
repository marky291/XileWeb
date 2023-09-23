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
        $schedule->job(new PostGuildPointsToDiscordJob('Kriemhild', today()))->at('18:00')->name('Kriemhild Points')->withoutOverlapping();
        $schedule->job(new PostGuildPointsToDiscordJob('Swanhild', today()))->at('18:00')->name('Swanhild Points')->withoutOverlapping();
        $schedule->job(new PostGuildPointsToDiscordJob('Fadhringh', today()))->at('18:00')->name('Fadhringh Points')->withoutOverlapping();
        $schedule->job(new PostGuildPointsToDiscordJob('Skoegul', today()))->at('18:00')->name('Skoegul Points')->withoutOverlapping();
        $schedule->job(new PostGuildPointsToDiscordJob('Gondul', today()))->at('18:00')->name('Gondul Points')->withoutOverlapping();
        $schedule->job(new PostGuildPointsToDiscordJob('Hljod', today()))->at('18:00')->name('Hljod Points')->withoutOverlapping();
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
