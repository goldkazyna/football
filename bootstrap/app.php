<?php

use App\Http\Middleware\CheckNotBlocked;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\CheckSubscription;
use App\Http\Middleware\CheckVerified;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => CheckRole::class,
            'verified.doctor' => CheckVerified::class,
            'subscription' => CheckSubscription::class,
            'not.blocked' => CheckNotBlocked::class,
        ]);

        $middleware->web(append: [
            CheckNotBlocked::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
