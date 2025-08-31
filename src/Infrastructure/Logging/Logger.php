<?php

declare(strict_types=1);

namespace App\Infrastructure\Logging;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use RuntimeException;

final class Logger
{
    private static ?MonologLogger $instance = null;

    /**
     * Возвращает синглтон-экземпляр логгера
     */
    public static function getInstance(): MonologLogger
    {
        if (self::$instance === null) {
            self::$instance = new MonologLogger('php_2025');

            $logDir = __DIR__ . '/../../../storage/logs';
            if (!mkdir($logDir, 0755, true) && !is_dir($logDir)) {
                throw new RuntimeException('Failed to create directory for logs: ' . $logDir);
            }

            $fileHandler = new StreamHandler($logDir . '/app.log', MonologLogger::INFO);

            $formatter = new LineFormatter(
                "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",
                'Y-m-d H:i:s'
            );
            $fileHandler->setFormatter($formatter);

            self::$instance->pushHandler($fileHandler);
        }

        return self::$instance;
    }
}
