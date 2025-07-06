<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use App\Exceptions\ExceptionHandlerRegistry;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
        $middleware->alias([
            'ngrok.https' => \App\Http\Middleware\UpgradeToHttpsUnderNgrok::class,
            'brotli.compress' => \App\Http\Middleware\CompressBrotliResponse::class,
            'auth' => \App\Http\Middleware\Authenticate::class
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        // Schedule commands here
        //$schedule->command('about')->everyMinute()->appendOutputTo(storage_path('logs/schedule-output.log'));
        //$schedule->command('test:scheduler')->everyFiveMinutes()->appendOutputTo(storage_path('logs/schedule-output.log'));
        $schedule->command('socket:check-timeouts')->everyMinute()->appendOutputTo(storage_path('logs/schedule-output.log'));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Register custom exception handlers
        (new ExceptionHandlerRegistry())($exceptions);
    })->create();
