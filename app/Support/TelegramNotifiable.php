<?php

namespace App\Support;

use App\Services\TelegramService;

/**
 * Helper trait for easy Telegram notifications throughout the application
 */
trait TelegramNotifiable
{
    /**
     * Send error notification to Telegram
     */
    public function notifyTelegramError(string $title, string $message, array $context = []): bool
    {
        return app(TelegramService::class)->notifyError($title, $message, $context);
    }

    /**
     * Send exception notification to Telegram
     */
    public function notifyTelegramException(\Throwable $exception, array $context = []): bool
    {
        return app(TelegramService::class)->notifyException($exception, $context);
    }

    /**
     * Send form submission error to Telegram
     */
    public function notifyTelegramSubmission(string $formName, string $error, array $data = []): bool
    {
        return app(TelegramService::class)->notifySubmissionError($formName, $error, $data);
    }

    /**
     * Send auth event to Telegram
     */
    public function notifyTelegramAuth(string $eventType, string $username, array $context = []): bool
    {
        return app(TelegramService::class)->notifyAuthEvent($eventType, $username, $context);
    }
}
