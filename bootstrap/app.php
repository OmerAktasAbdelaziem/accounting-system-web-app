<?php

use App\Services\TelegramService;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

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
            'super_admin.telegram_errors' => \App\Http\Middleware\NotifySuperAdminErrors::class,
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
        $exceptions->reportable(function (Throwable $e) {
            try {
                $routeName = optional(request()->route())->getName();
                if (str_starts_with((string) $routeName, 'super-admin.') || request()->is('super-admin*')) {
                    return;
                }

                $telegramService = app(TelegramService::class);

                $context = [
                    'url' => request()->fullUrl(),
                    'method' => request()->method(),
                    'ip' => request()->ip(),
                    'user' => auth()->check() ? auth()->user()->email : 'Guest',
                ];

                $statusCode = 500;
                if ($e instanceof HttpExceptionInterface || $e instanceof HttpException) {
                    $statusCode = $e->getStatusCode();
                }

                if ($statusCode >= 500) {
                    $telegramService->notifyHttpError($statusCode, $e, $context);
                } elseif ($statusCode >= 400) {
                    $telegramService->notifyHttpError($statusCode, $e, $context);
                } else {
                    $telegramService->notifyException($e, $context);
                }
            } catch (\Throwable $telegramError) {
                \Log::error('Failed to send Telegram exception notification', [
                    'error' => $telegramError->getMessage(),
                    'exception' => get_class($telegramError),
                ]);
            }
        });
    })->create();
