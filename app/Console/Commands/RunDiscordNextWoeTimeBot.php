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
        $pronteraTimes = config('castle.prontera');

        // Create an array to store all Prontera times
        $allPronteraTimes = [];

        // Loop through each castle in Prontera
        foreach ($pronteraTimes as $castle => $castleDetails) {
            // Check if the castle is open
            if ($castleDetails['open']) {
                // Add the time to the array
                $allPronteraTimes[] = $castleDetails['time'];
            }
        }

        // Now, $allPronteraTimes is an array containing all open times for Prontera
        print_r($allPronteraTimes);

        dd($allPronteraTimes);

        RunDiscordPythonBot::run($this, "Next Woe Time", [
            ''
        ]);
    }
}
