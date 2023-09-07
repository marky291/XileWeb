<?php

namespace App\Jobs;

use App\Models\EventWoe;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class GuildMessagesToDiscord implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $webhookUrl = config('services.discord.channel_guild_events');

        $content = <<<'HTML'
        **Current Point Standings:**
        🛡️ Longest Castle Defense: 3 points - Yellow Card
        🏰 Castle Owner: 2 points - Astral Godz
        ⚔️ Most Valuable Guild: 1 point - Yellow Card
        🗡️ Most Valuable Breaker: 1 point - Astral Godz
        HTML;

        Http::post($webhookUrl, [
            'content' => $content,
        ]);
    }


}
