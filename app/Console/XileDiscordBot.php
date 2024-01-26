<?php

namespace App\Console;

use App\Console\Commands\Log;
use App\Console\Commands\Process;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class XileDiscordBot extends Command
{
    public function RunBot(string $botName, string $discordToken)
    {
        set_time_limit(0);
        pcntl_async_signals(true); // Enable signal handling

        $botNameSlug = Str::slug($botName);
        $scriptPath = base_path("app/Discord/scripts/{$botNameSlug}.py");
        $token = config("services.discord.{$botNameSlug}");
        $url = route('api.discord');

        $process = new Process(['python3', $scriptPath, $token, $url]);
        $process->setOptions(['create_process_group' => true]);
        $process->setTimeout(3600);

        // Register signal handlers
        pcntl_signal(SIGTERM, function () use ($process) {
            $process->stop();
        });

        pcntl_signal(SIGINT, function () use ($process) {
            $process->stop();
        });

        $process->mustRun(function ($type, $buffer, $botName) {
            $logMethod = Process::ERR === $type ? 'error' : 'info';
            Log::$logMethod("Discord {$botName} Bot: " . $buffer);
            $this->output->write($buffer);
        });

        if (!$process->isSuccessful()) {
            Log::error("Error running Discord {$botName} Bot: " . $process->getErrorOutput());
        }

        $this->info("Discord {$botName} Bot executed successfully.")u
    }
}
