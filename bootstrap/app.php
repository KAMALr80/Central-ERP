<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\StaffMiddleware;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\CheckRole;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // 👇 Register middleware aliases
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'hr'    => \App\Http\Middleware\HRMiddleware::class,
            'staff' => StaffMiddleware::class,

            // ✅ Add role middleware for dynamic role checking
            'role' => RoleMiddleware::class,
            'check.role' => CheckRole::class,
        ]);

        // ✅ Add CSRF exception for API routes (FIX FOR 419 ERROR)
        $middleware->validateCsrfTokens(except: [
            'api/*',
            'api/app/login',
            'api/shipments/*',
            'api/agents/*',
            'api/track/*',
            'api/routes/*',
            'api/locations/*',
            'api/public/*',
            'api/test',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
