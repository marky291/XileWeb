<?php

namespace App\Console\Commands;

use App\Actions\RunDiscordPythonBot;
use Illuminate\Console\Command;

class RunDiscordNextWoeTimeBot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-discord-next-woe-time-bot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pronteraTimes = config('castles.prontera');

        // Create an array to store all Prontera times
        $allPronteraTimes = [];

        // Loop through each castle in Prontera
        foreach ($pronteraTimes as $castle => $castleDetails) {
            // Check if the castle is open
            if ($castleDetails['open']) {
                // Add castle name and time to the array
                $allPronteraTimes[$castle] = $castleDetails['time'];
            }
        }

        // Find the minimum WoE time
        $nextWoeTime = min($allPronteraTimes);

        // Get the current time
        $currentTime = now();

        // Calculate the time difference
        $timeDifference = $currentTime->diffInHours($nextWoeTime);

        // Display the time until the next WoE in hours
        $this->info("Next WoE in {$timeDifference} hours");

        // Pass the information to the Discord bot
        RunDiscordPythonBot::run($this, "Next Woe Time", [
            'nextWoeTime' => $nextWoeTime,
            'timeDifference' => $timeDifference,
        ]);

        RunDiscordPythonBot::run($this, "Next Woe Time", $allPronteraTimes);
    }
}
