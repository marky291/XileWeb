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
        set_time_limit(0);

        $scriptPath = base_path('app/Discord/scripts/discord-bot.py');
        $token = config('services.discord.uber_cost_token');
        $url = route('api.discord');

        $process = new Process(['python3', $scriptPath, $token, $url]);
        $process->setTimeout(3600);

        // Directly send the output to log and console without buffering
        $process->mustRun(function ($type, $buffer) {
            $logMethod = Process::ERR === $type ? 'error' : 'info';
            Log::$logMethod("Discord Uber Cost Bot: " . $buffer);
            $this->output->write($buffer);
        });

        if (!$process->isSuccessful()) {
            Log::error("Error running Discord Uber Cost Bot: " . $process->getErrorOutput());
        }

        $this->info('Discord Uber Cost Bot executed successfully.');
    }

}
