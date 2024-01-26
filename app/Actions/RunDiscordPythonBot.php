<?php

namespace App\Actions;

use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class RunDiscordPythonBot
{
    use AsAction;

    public function handle(Command $command, string $botName, array $data = [])
    {

        set_time_limit(0);
        pcntl_async_signals(true); // Enable signal handling

        $botNameSlug = Str::slug($botName);
        $scriptPath = base_path("app/Discord/scripts/{$botNameSlug}.py");
        $token = config("services.discord.{$botNameSlug}");
        $url = route('api.discord');

        $process = new Process(array_merge(['python3', $scriptPath, $token, $url], $data));
        $process->setOptions(['create_process_group' => true]);
        $process->setTimeout(3600);

        // Register signal handlers
        pcntl_signal(SIGTERM, function () use ($process) {
            $process->stop();
        });

        pcntl_signal(SIGINT, function () use ($process) {
            $process->stop();
        });

        $process->mustRun(function ($type, $buffer, $botName, $command) {
            $logMethod = Process::ERR === $type ? 'error' : 'info';
            Log::$logMethod("Discord {$botName} Bot: " . $buffer);
            $command->output->write($buffer);
        });

        if (!$process->isSuccessful()) {
            Log::error("Error running Discord {$botName} Bot: " . $process->getErrorOutput());

            $command->info("Discord {$botName} Bot Failed.");
        }

        $command->info("Discord {$botName} Bot executed successfully.")u
    }
}
