<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register route middleware aliases
        $middleware->alias([
            'check-api-token' => \App\Http\Middleware\CheckApiToken::class,
            'rate-limit' => \App\Http\Middleware\RateLimitMiddleware::class,
            'security-headers' => \App\Http\Middleware\SecurityHeadersMiddleware::class,
            'super_admin' => \App\Http\Middleware\SuperAdminMiddleware::class,
        ]);

        // Apply locale middleware to web requests after session starts
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);

        // Apply global middleware
        $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);
        $middleware->append(\App\Http\Middleware\AuditLoggingMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
