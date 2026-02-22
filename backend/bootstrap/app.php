<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',        // <-- load our API routes
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register 'role' as an alias for our CheckRole middleware
        // Usage: ->middleware('role:admin') or ->middleware('role:admin,manager')
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);

        // Allow the React frontend (localhost:3000) to use Sanctum tokens
        // $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
