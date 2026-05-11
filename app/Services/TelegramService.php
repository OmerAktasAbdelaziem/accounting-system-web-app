<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    private string $token;
    private string $chatId;
    private string $apiUrl;
    private bool $enabled;

    public function __construct()
    {
        $this->token = config('telegram.token');
        $this->chatId = config('telegram.chat_id');
        $this->apiUrl = config('telegram.api_url');
        $this->enabled = config('telegram.enabled');
    }

    /**
     * Send a message to Telegram
     */
    public function sendMessage(string $message, array $options = []): bool
    {
        if (!$this->enabled || !$this->token || !$this->chatId) {
            return false;
        }

        try {
            $payload = array_merge([
                'chat_id' => $this->chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ], $options);

            $response = Http::timeout(10)
                ->post("{$this->apiUrl}/bot{$this->token}/sendMessage", $payload);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Telegram send message failed', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send error notification
     */
    public function notifyError(string $errorTitle, string $errorMessage, array $context = []): bool
    {
        if (!config('telegram.notifications.errors')) {
            return false;
        }

        $message = $this->formatErrorMessage($errorTitle, $errorMessage, $context);
        return $this->sendMessage($message);
    }

    /**
     * Send exception notification
     */
    public function notifyException(\Throwable $exception, array $context = []): bool
    {
        if (!config('telegram.notifications.exceptions')) {
            return false;
        }

        $message = $this->formatExceptionMessage($exception, $context);
        return $this->sendMessage($message);
    }

    /**
     * Send submission error notification
     */
    public function notifySubmissionError(string $formName, string $errorMessage, array $data = []): bool
    {
        if (!config('telegram.notifications.submissions')) {
            return false;
        }

        $message = $this->formatSubmissionErrorMessage($formName, $errorMessage, $data);
        return $this->sendMessage($message);
    }

    /**
     * Send auth event notification
     */
    public function notifyAuthEvent(string $eventType, string $username, array $context = []): bool
    {
        if (!config('telegram.notifications.auth')) {
            return false;
        }

        $message = $this->formatAuthEventMessage($eventType, $username, $context);
        return $this->sendMessage($message);
    }

    /**
     * Format error message with HTML styling
     */
    private function formatErrorMessage(string $title, string $message, array $context = []): string
    {
        $timestamp = now()->format('Y-m-d H:i:s');
        $app = config('app.name', 'Accounting System');
        
        $formatted = "🚨 <b>Error Notification</b>\n";
        $formatted .= "━━━━━━━━━━━━━━━━━━━━\n";
        $formatted .= "📱 App: <b>{$app}</b>\n";
        $formatted .= "⏰ Time: <code>{$timestamp}</code>\n";
        $formatted .= "❌ Title: <b>{$title}</b>\n";
        $formatted .= "📝 Message: <code>{$message}</code>\n";

        if (!empty($context)) {
            $formatted .= "\n<b>Context:</b>\n";
            foreach ($context as $key => $value) {
                $value = is_array($value) ? json_encode($value) : $value;
                $formatted .= "• <code>{$key}:</code> <code>" . substr($value, 0, 100) . "</code>\n";
            }
        }

        return $formatted;
    }

    /**
     * Format exception message with full details
     */
    private function formatExceptionMessage(\Throwable $exception, array $context = []): string
    {
        $timestamp = now()->format('Y-m-d H:i:s');
        $app = config('app.name', 'Accounting System');
        $env = app()->environment();
        
        $formatted = "🔴 <b>Exception Alert</b>\n";
        $formatted .= "━━━━━━━━━━━━━━━━━━━━\n";
        $formatted .= "📱 App: <b>{$app}</b> ({$env})\n";
        $formatted .= "⏰ Time: <code>{$timestamp}</code>\n";
        $formatted .= "🔗 Exception: <b>" . get_class($exception) . "</b>\n";
        $formatted .= "📄 File: <code>" . $exception->getFile() . "</code>\n";
        $formatted .= "📍 Line: <code>" . $exception->getLine() . "</code>\n";
        $formatted .= "💬 Message: <code>" . substr($exception->getMessage(), 0, 200) . "</code>\n";

        if (!empty($context)) {
            $formatted .= "\n<b>Additional Context:</b>\n";
            foreach (array_slice($context, 0, 5) as $key => $value) {
                $value = is_array($value) ? json_encode($value) : $value;
                $formatted .= "• <code>{$key}:</code> <code>" . substr($value, 0, 80) . "</code>\n";
            }
        }

        return $formatted;
    }

    /**
     * Format submission error message
     */
    private function formatSubmissionErrorMessage(string $formName, string $errorMessage, array $data = []): string
    {
        $timestamp = now()->format('Y-m-d H:i:s');
        $app = config('app.name', 'Accounting System');
        $user = auth()->user();
        
        $formatted = "⚠️ <b>Form Submission Error</b>\n";
        $formatted .= "━━━━━━━━━━━━━━━━━━━━\n";
        $formatted .= "📱 App: <b>{$app}</b>\n";
        $formatted .= "⏰ Time: <code>{$timestamp}</code>\n";
        $formatted .= "📋 Form: <b>{$formName}</b>\n";
        $formatted .= "❌ Error: <code>" . substr($errorMessage, 0, 150) . "</code>\n";
        
        if ($user) {
            $formatted .= "👤 User: <b>" . $user->email . "</b>\n";
        }

        if (!empty($data)) {
            $formatted .= "\n<b>Form Data:</b>\n";
            foreach (array_slice($data, 0, 4) as $key => $value) {
                if (is_string($value) && strlen($value) > 50) {
                    $value = substr($value, 0, 50) . '...';
                }
                $value = is_array($value) ? json_encode($value) : $value;
                $formatted .= "• <code>{$key}:</code> <code>{$value}</code>\n";
            }
        }

        return $formatted;
    }

    /**
     * Format auth event message
     */
    private function formatAuthEventMessage(string $eventType, string $username, array $context = []): string
    {
        $timestamp = now()->format('Y-m-d H:i:s');
        $app = config('app.name', 'Accounting System');
        $icon = match ($eventType) {
            'login' => '✅',
            'logout' => '🚪',
            'failed' => '❌',
            'locked' => '🔒',
            default => 'ℹ️',
        };

        $formatted = "{$icon} <b>Authentication Event</b>\n";
        $formatted .= "━━━━━━━━━━━━━━━━━━━━\n";
        $formatted .= "📱 App: <b>{$app}</b>\n";
        $formatted .= "⏰ Time: <code>{$timestamp}</code>\n";
        $formatted .= "🔐 Event: <b>" . ucfirst($eventType) . "</b>\n";
        $formatted .= "👤 Username: <b>{$username}</b>\n";

        if (!empty($context)) {
            $formatted .= "\n<b>Details:</b>\n";
            foreach (array_slice($context, 0, 3) as $key => $value) {
                $value = is_array($value) ? json_encode($value) : $value;
                $formatted .= "• <code>{$key}:</code> <code>" . substr($value, 0, 80) . "</code>\n";
            }
        }

        return $formatted;
    }
}
