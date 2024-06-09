<?php

namespace App\WoeEvents;

use Illuminate\Support\Facades\Http;
use Lorisleiva\Actions\Concerns\AsAction;

class WoeEventDiscordChannelSender
{
    use AsAction;

    public function handle(WoeEventScoreRecorder $recorder, string $webhookUrl, string $message)
    {
        Http::post($webhookUrl, [
            'content' => '',
            'embeds' => [
                [
                    'title' => "<a:verifiyblue:1079199354172219562> Guild of the Month (Season {$recorder->season})",
                    'description' => $message,
                    'color' => hexdec('FF0000'),
                ],
            ],
        ]);
    }
}
