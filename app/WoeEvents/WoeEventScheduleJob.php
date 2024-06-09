<?php

namespace App\WoeEvents;

use App\Actions\ProcessWoeEventPoints;
use App\Exceptions\WoeEventNotEnoughEventsToProcessException;
use DateTime;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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

        foreach ($castles as $castle) {
            try {
                $discord_webhook = WoeEventDiscordChannelResolver::run($castle);
                /** @var WoeEventScoreRecorder $recorder */
                $recorder = ProcessWoeEventPoints::run($castle);
                $message = WoeEventDiscordMessage::run($recorder, $castle);
                WoeEventDiscordChannelSender::run($recorder, $discord_webhook, $message);
            } catch (WoeEventNotEnoughEventsToProcessException $e) {
                // Swallow the exception, no action is taken, and the loop continues
            } catch (Exception $exception) {
                // Log other exceptions and continue the loop
                Log::error("Exception for castle $castle: " . $exception->getMessage());
            }
        }
    }
}
