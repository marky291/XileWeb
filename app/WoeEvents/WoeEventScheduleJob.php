<?php

namespace App\WoeEvents;

use App\Actions\ProcessWoeEventPoints;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WoeEventScheduleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct() {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
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
            $discord_webhook = WoeEventDiscordChannelResolver::run($castle);
            $points = ProcessWoeEventPoints::run($castle);
            $message = WoeEventDiscordMessage::run($points, $castle);
            WoeEventDiscordChannelSender::run($discord_webhook, $message);
        }
    }
}
