<?php

namespace App\WoeEvents;

use Illuminate\Support\Facades\Http;
use Lorisleiva\Actions\Concerns\AsAction;

class WoeEventDiscordChannelSender
{
    use AsAction;

    public function handle(string $webhookUrl, string $message)
    {
        Http::post($webhookUrl, [
            'content' => $message,
        ]);
    }
}
