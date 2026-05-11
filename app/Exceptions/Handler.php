<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Services\TelegramService;

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
                $telegramService = app(TelegramService::class);
                $context = [
                    'url' => request()->fullUrl(),
                    'method' => request()->method(),
                    'ip' => request()->ip(),
                    'user' => auth()->check() ? auth()->user()->email : 'Guest',
                ];
                $telegramService->notifyException($e, $context);
            } catch (\Exception $telegramError) {
                // Silently fail if Telegram notification fails
                \Log::error('Failed to send Telegram exception notification', [
                    'error' => $telegramError->getMessage(),
                ]);
            }
        });
    }
}
