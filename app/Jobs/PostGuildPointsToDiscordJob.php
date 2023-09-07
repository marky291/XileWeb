<?php

namespace App\Jobs;

use App\Actions\ProcessWoeEventPoints;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PostGuildPointsToDiscordJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $castle, public DateTime $dateTime) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        (new ProcessWoeEventPoints)->handle($this->castle, $this->dateTime, config('xilero.woe_events.season'), true);
    }
}
