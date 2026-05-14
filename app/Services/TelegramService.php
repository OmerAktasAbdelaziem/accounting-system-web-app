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
     * Send HTTP 500 error notification with diagnostic information
     */
    public function notifyHttpError(int $statusCode, \Throwable $exception, array $context = []): bool
    {
        if (!config('telegram.notifications.exceptions')) {
            return false;
        }

        $message = $this->formatHttpErrorMessage($statusCode, $exception, $context);
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

    /**
     * Format HTTP error message with solutions
     */
    private function formatHttpErrorMessage(int $statusCode, \Throwable $exception, array $context = []): string
    {
        $timestamp = now()->format('Y-m-d H:i:s');
        $app = config('app.name', 'Accounting System');
        $env = app()->environment();
        
        $statusIcon = match ($statusCode) {
            500 => '🔴',
            503 => '🟠',
            404 => '⚠️',
            default => '🚨',
        };

        $formatted = "{$statusIcon} <b>HTTP {$statusCode} Error</b>\n";
        $formatted .= "━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $formatted .= "📱 App: <b>{$app}</b> ({$env})\n";
        $formatted .= "⏰ Time: <code>{$timestamp}</code>\n";
        $formatted .= "📄 Status: <b>{$statusCode}</b> - " . $this->getHttpStatusMessage($statusCode) . "\n";

        if (!empty($context['url'])) {
            $formatted .= "🔗 URL: <code>" . substr($context['url'], 0, 100) . "</code>\n";
        }
        if (!empty($context['method'])) {
            $formatted .= "📌 Method: <code>" . $context['method'] . "</code>\n";
        }
        if (!empty($context['user'])) {
            $formatted .= "👤 User: <code>" . $context['user'] . "</code>\n";
        }
        if (!empty($context['ip'])) {
            $formatted .= "🌐 IP: <code>" . $context['ip'] . "</code>\n";
        }

        $formatted .= "\n<b>🔗 Exception Details:</b>\n";
        $formatted .= "• Class: <code>" . get_class($exception) . "</code>\n";
        $formatted .= "• File: <code>" . str_replace('\\', '/', $exception->getFile()) . "</code>\n";
        $formatted .= "• Line: <code>" . $exception->getLine() . "</code>\n";
        $formatted .= "• Message: <code>" . substr($exception->getMessage(), 0, 150) . "</code>\n";

        // Add solutions
        $solutions = $this->getSolutionsForException($exception, $statusCode);
        if (!empty($solutions)) {
            $formatted .= "\n<b>💡 Possible Solutions:</b>\n";
            foreach (array_slice($solutions, 0, 3) as $solution) {
                $formatted .= "✓ <code>" . $solution . "</code>\n";
            }
        }

        // Add stack trace snippet
        if (app()->environment('local', 'testing')) {
            $formatted .= "\n<b>📚 Stack Trace (First 2 frames):</b>\n";
            $trace = array_slice($exception->getTrace(), 0, 2);
            foreach ($trace as $index => $frame) {
                $function = $frame['function'] ?? 'unknown';
                $class = $frame['class'] ?? '';
                $file = str_replace('\\', '/', $frame['file'] ?? 'unknown');
                $line = $frame['line'] ?? 0;
                $formatted .= "  [{$index}] <code>{$class}{$function}() at {$file}:{$line}</code>\n";
            }
        }

        return $formatted;
    }

    /**
     * Get HTTP status message
     */
    private function getHttpStatusMessage(int $statusCode): string
    {
        return match ($statusCode) {
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            413 => 'Payload Too Large',
            414 => 'URI Too Long',
            415 => 'Unsupported Media Type',
            429 => 'Too Many Requests',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            default => 'Unknown Error',
        };
    }

    /**
     * Get solutions for specific exceptions
     */
    private function getSolutionsForException(\Throwable $exception, int $statusCode): array
    {
        $exceptionClass = get_class($exception);
        $solutions = [];

        // Database errors
        if (stripos($exceptionClass, 'PDOException') !== false || stripos($exception->getMessage(), 'database') !== false) {
            $solutions = [
                'Check database connection and credentials in .env file',
                'Verify database server is running',
                'Check if migrations have been run: php artisan migrate',
                'Check table and column names in your query',
            ];
        }
        // Model not found
        elseif (stripos($exceptionClass, 'ModelNotFoundException') !== false) {
            $solutions = [
                'Verify the resource ID exists in the database',
                'Check the route parameter and its value',
                'Use Model::findOrFail() instead to handle missing models',
            ];
        }
        // Method not found
        elseif (stripos($exceptionClass, 'BadMethodCallException') !== false) {
            $solutions = [
                'Check if the method exists on the object/class',
                'Verify method name spelling and case sensitivity',
                'Check if the class is properly defined and extended',
            ];
        }
        // File not found
        elseif (stripos($exceptionClass, 'FileNotFoundException') !== false) {
            $solutions = [
                'Verify the file path is correct',
                'Check file permissions',
                'Ensure the file exists in the expected location',
            ];
        }
        // Validation errors
        elseif (stripos($exceptionClass, 'ValidationException') !== false) {
            $solutions = [
                'Check the validation rules in your controller',
                'Verify all required fields are provided with correct format',
                'Review the error messages for specific field validation failures',
            ];
        }
        // Authentication errors
        elseif (stripos($exceptionClass, 'AuthenticationException') !== false || stripos($exceptionClass, 'AuthorizationException') !== false) {
            $solutions = [
                'Ensure user is logged in',
                'Verify user permissions for this resource',
                'Check authentication middleware configuration',
            ];
        }
        // Syntax/Parse errors
        elseif (stripos($exceptionClass, 'ParseError') !== false) {
            $solutions = [
                'Check for syntax errors in the mentioned file and line',
                'Verify PHP syntax with: php -l <filename>',
                'Look for missing quotes, braces, or semicolons',
            ];
        }
        // Connection timeout
        elseif (stripos($exception->getMessage(), 'timeout') !== false) {
            $solutions = [
                'Check network connectivity',
                'Increase timeout value in configuration',
                'Verify external service is reachable',
            ];
        }
        // Generic HTTP 500 errors
        else {
            $solutions = [
                'Check Laravel logs: storage/logs/laravel.log',
                'Enable debug mode in .env (APP_DEBUG=true)',
                'Run: php artisan config:clear && php artisan cache:clear',
                'Verify all dependencies are installed: composer install',
                'Check file permissions in storage and bootstrap/cache directories',
            ];
        }

        return $solutions;
    }

    /**
     * Get system diagnostics information
     */
    private function getSystemDiagnostics(): array
    {
        return [
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time') . 's',
            'environment' => app()->environment(),
            'debug_mode' => config('app.debug') ? 'Enabled' : 'Disabled',
            'database' => config('database.default'),
            'cache' => config('cache.default'),
            'queue' => config('queue.default'),
        ];
    }

    /**
     * Send detailed error report with system diagnostics
     */
    public function notifyDetailedError(\Throwable $exception, string $title = 'System Error', array $extra = []): bool
    {
        if (!config('telegram.notifications.exceptions')) {
            return false;
        }

        $timestamp = now()->format('Y-m-d H:i:s');
        $app = config('app.name', 'Accounting System');
        
        $formatted = "🚨 <b>{$title}</b>\n";
        $formatted .= "━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $formatted .= "📱 App: <b>{$app}</b>\n";
        $formatted .= "⏰ Time: <code>{$timestamp}</code>\n";
        $formatted .= "🔗 Exception: <b>" . get_class($exception) . "</b>\n";
        $formatted .= "📄 File: <code>" . str_replace('\\', '/', $exception->getFile()) . "</code>\n";
        $formatted .= "📍 Line: <code>" . $exception->getLine() . "</code>\n";
        $formatted .= "💬 Message: <code>" . substr($exception->getMessage(), 0, 150) . "</code>\n";

        if (!empty($extra)) {
            $formatted .= "\n<b>📋 Additional Info:</b>\n";
            foreach (array_slice($extra, 0, 4) as $key => $value) {
                $value = is_array($value) ? json_encode($value) : $value;
                $formatted .= "• <code>{$key}:</code> <code>" . substr($value, 0, 80) . "</code>\n";
            }
        }

        // Add system diagnostics
        $diagnostics = $this->getSystemDiagnostics();
        $formatted .= "\n<b>⚙️ System Info:</b>\n";
        foreach ($diagnostics as $key => $value) {
            $formatted .= "• <code>{$key}:</code> <code>{$value}</code>\n";
        }

        return $this->sendMessage($formatted);
    }
}

