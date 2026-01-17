<?php

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->report(function (QueryException $e) {
            $connection = $e->getConnectionName();
            $gameConnections = ['xilero_main', 'xilero_logs', 'xileretro_main', 'xileretro_logs'];

            if (in_array($connection, $gameConnections)) {
                Log::channel('single')->error("Game database unavailable: {$connection}", [
                    'connection' => $connection,
                    'error' => $e->getMessage(),
                    'code' => $e->getCode(),
                ]);
            }
        });
    })
    ->withSchedule(function ($schedule) {
        //
    })
    ->create();
