<?php

namespace App\Logging;

use Illuminate\Log\Logger;
use App\Services\TelegramService;
use Monolog\LogRecord;

class TelegramLogHandler
{
    /**
     * Handle a log entry for Telegram
     */
    public function __invoke(Logger $logger)
    {
        $telegramService = app(TelegramService::class);

        foreach ($logger->getHandlers() as $handler) {
            $handler->pushProcessor(function (LogRecord $record) use ($telegramService) {
                // Only send critical errors and above to Telegram
                if ($record->level->value >= \Psr\Log\LogLevel::ERROR) {
                    $telegramService->notifyError(
                        'System Log: ' . $record->level->name,
                        $record->message,
                        $record->context ?? []
                    );
                }

                return $record;
            });
        }
    }
}
