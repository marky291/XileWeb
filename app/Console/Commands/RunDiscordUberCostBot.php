<?php

namespace App\Console\Commands;

use App\Ragnarok\ServerZeny;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;
use function Symfony\Component\String\u;

class RunDiscordUberCostBot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-discord-uber-cost-bot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the Discord Uber Cost Bot.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        set_time_limit(0);  // Remove the PHP execution time limit

        $scriptPath = base_path('app/Discord/scripts/discord-bot.py');

        $token = config('services.discord.uber_cost_token');

        $url = route('api.discord');

        $process = new Process(['python3', $scriptPath, $token, $url]);

        $process->setTimeout(null);

        // Run the script and provide feedback in real-time
        $process->run(function ($type, $buffer) {
            if (Process::ERR === $type) {
                Log::error("Discord Uber Cost Bot error: " . $buffer);
                $this->error($buffer);  // Send error to console
            } else {
                Log::info($buffer);
                $this->info($buffer);  // Send output to console
            }
        });

        if (!$process->isSuccessful()) {
            Log::error("Error running Discord Uber Cost Bot: " . $process->getErrorOutput());
        }

        $this->info('Discord Uber Cost Bot executed successfully.');
    }
}
