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
        set_time_limit(0);
        pcntl_async_signals(true); // Enable signal handling

        $scriptPath = base_path('app/Discord/scripts/player-count.py');
        $token = config('services.discord.player_count_token');
        $url = route('api.discord');

        $process = new Process(['python3', $scriptPath, $token, $url]);
        $process->setOptions(['create_process_group' => true]);
        $process->setTimeout(null);

        // Register signal handlers
        pcntl_signal(SIGTERM, function () use ($process) {
            $process->stop();
        });

        pcntl_signal(SIGINT, function () use ($process) {
            $process->stop();
        });

        $process->mustRun(function ($type, $buffer) {
            $logMethod = Process::ERR === $type ? 'error' : 'info';
            Log::$logMethod("Discord Player Count: " . $buffer);
            $this->output->write($buffer);
        });

        if (!$process->isSuccessful()) {
            Log::error("Error running player count discord bot: " . $process->getErrorOutput());
        }

        $this->info('Discord player count executed successfully.');
    }
}
