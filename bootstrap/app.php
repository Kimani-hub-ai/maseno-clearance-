<?php

/**
 * FILE: bootstrap/app.php
 *
 * Replace your existing bootstrap/app.php with this content.
 * The key addition is ->withMiddleware() registering 'role' as an alias
 * for RoleMiddleware so that route middleware('role:student') works.
 */

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register the role middleware alias
        // This allows: Route::middleware('role:admin') in web.php
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();