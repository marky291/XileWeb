<?php

namespace App\Console\Commands;

use App\Ragnarok\Char;
use Illuminate\Console\Command;
use Log;
use Symfony\Component\Process\Process;

class RunDiscordPlayerCountBot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-discord-player-count-bot';

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

        $scriptPath = base_path('app/Discord/scripts/player-count.py');

        $token = config('services.discord.player_count_token');

        $player_count = number_format(Char::query()->online()->count() ?? 0);

        $process = new Process(['python3', $scriptPath, $token, $player_count]);

        $process->setTimeout(null);

        // Run the script and provide feedback in real-time
        $process->run(function ($type, $buffer) {
            if (Process::ERR === $type) {
                Log::error("Discord Player Count Error: " . $buffer);
                $this->error($buffer);  // Send error to console
            } else {
                Log::info($buffer);
                $this->info($buffer);  // Send output to console
            }
        });

        if (!$process->isSuccessful()) {
            Log::error("Error running player count discord bot: " . $process->getErrorOutput());
        }

        $this->info('Discord player count executed successfully.');
    }
}
