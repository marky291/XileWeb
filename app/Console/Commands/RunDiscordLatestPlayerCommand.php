<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class RunDiscordLatestPlayerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-discord-latest-player-command';

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
        set_time_limit(0);  // Remove the PHP execution time limit

        $scriptPath = base_path('app/Discord/scripts/latest-player.py');

        $token = config('services.discord.latest_player_token');

        $url = route('api.discord');

        $process = new Process(['python3', $scriptPath, $token, $url]);

        $process->setTimeout(null);

        // Run the script and provide feedback in real-time
        $process->run(function ($type, $buffer) {
            if (Process::ERR === $type) {
                Log::error("Discord Latest Player Error: " . $buffer);
                $this->error($buffer);  // Send error to console
            } else {
                Log::info($buffer);
                $this->info($buffer);  // Send output to console
            }
        });

        if (!$process->isSuccessful()) {
            Log::error("Error running latest player discord bot: " . $process->getErrorOutput());
        }

        $this->info('Discord latest player executed successfully.');
    }
}