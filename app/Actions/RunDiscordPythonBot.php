<?php

namespace App\Actions;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
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

        $botNameSlug = Str::of($botName)->replace(' ', '_')->lower();
        $scriptPath = base_path("app/Discord/scripts/{$botNameSlug}.py");
        $token = config("services.discord.{$botNameSlug}_token");

        if ($token == null) {
            throw new \Exception("Token missing for discord bot {$botName}.");
        }

        $url = route('api.discord');

        $data = json_encode($data, JSON_HEX_QUOT | JSON_HEX_TAG);
        $process = new Process(['python3', $scriptPath, $token, $url, $data]);
        $process->setOptions(['create_process_group' => true]);
        $process->setTimeout(3600);

        // Register signal handlers
        pcntl_signal(SIGTERM, function () use ($process) {
            $process->stop();
        });

        pcntl_signal(SIGINT, function () use ($process) {
            $process->stop();
        });

        $process->mustRun(function ($type, $buffer) use ($command, $botName) {
            $logMethod = Process::ERR === $type ? 'error' : 'info';
            Log::$logMethod("Discord {$botName} Bot: " . $buffer);
            $command->getOutput()->write($buffer);
        });

        if (!$process->isSuccessful()) {
            Log::error("Error running Discord {$botName} Bot: " . $process->getErrorOutput());

            $command->info("Discord {$botName} Bot Failed.");
        }

        $command->info("Discord {$botName} Bot executed successfully.");
    }
}
