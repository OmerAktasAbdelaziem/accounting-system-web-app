<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\TelegramService;

class HandleFormSubmissionErrors
{
    protected TelegramService $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    public function handle(Request $request, Closure $next)
    {
        try {
            $response = $next($request);

            // Check if there are validation errors
            if ($response->status() === 422 || ($request->expectsJson() && !empty(session()->get('errors')))) {
                $errors = session()->get('errors', []);
                if (!empty($errors)) {
                    $this->notifySubmissionError(
                        $request,
                        'Validation Error',
                        $errors
                    );
                }
            }

            return $response;
        } catch (\Exception $e) {
            // Notify about form processing errors
            $this->telegramService->notifySubmissionError(
                $request->path(),
                $e->getMessage(),
                $request->all()
            );

            throw $e;
        }
    }

    /**
     * Send submission error notification
     */
    protected function notifySubmissionError(Request $request, string $errorType, array $errors)
    {
        try {
            $this->telegramService->notifySubmissionError(
                $request->path(),
                $errorType . ': ' . json_encode($errors),
                $request->all()
            );
        } catch (\Exception $e) {
            \Log::error('Failed to notify submission error via Telegram', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
