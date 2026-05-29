<?php

declare(strict_types=1);

namespace MkdBot\Infrastructure\Logging;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Level;
use Monolog\LogRecord;
use RuntimeException;
use UnexpectedValueException;

/**
 * Resilient RotatingFileHandler — не бросает исключения при ошибках записи.
 *
 * Если файл лога недоступен (например, проблемы с правами),
 * обработчик молча игнорирует ошибку вместо того чтобы уронить запрос.
 */
class ResilientRotatingFileHandler extends RotatingFileHandler
{
    public function __construct(
        string $filename,
        int $maxFiles = 0,
        int|string|Level $level = Level::Debug,
        bool $bubble = true,
        int $filePermissions = 0664,
    ) {
        parent::__construct($filename, $maxFiles, $level, $bubble, $filePermissions);
    }

    protected function write(LogRecord $record): void
    {
        try {
            parent::write($record);
        } catch (UnexpectedValueException|RuntimeException) {
            // Молча игнорируем ошибки записи — не даём логам уронить приложение
        }
    }
}
