<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Services\TelegramService;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Send to Telegram
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

                // Detect HTTP error status code
                $statusCode = 500;
                if ($e instanceof HttpExceptionInterface) {
                    $statusCode = $e->getStatusCode();
                } elseif ($e instanceof HttpException) {
                    $statusCode = $e->getStatusCode();
                }

                // Send different notification types based on HTTP status
                if ($statusCode >= 500) {
                    // For 5xx errors, use HTTP error notification with suggestions
                    $telegramService->notifyHttpError($statusCode, $e, $context);
                } elseif ($statusCode >= 400) {
                    // For 4xx errors, still notify but less critical
                    $telegramService->notifyHttpError($statusCode, $e, $context);
                } else {
                    // For other exceptions
                    $telegramService->notifyException($e, $context);
                }
            } catch (\Exception $telegramError) {
                // Silently fail if Telegram notification fails
                \Log::error('Failed to send Telegram exception notification', [
                    'error' => $telegramError->getMessage(),
                    'exception' => get_class($telegramError),
                ]);
            }
        });
    }
}
