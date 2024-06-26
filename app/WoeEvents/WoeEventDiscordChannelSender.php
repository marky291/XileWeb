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
                    'title' => "Guild of the Month (Season {$recorder->season})",
                    'description' => $message,
                    'color' => hexdec('FF0000'),
                ],
            ],
        ]);
    }
}
