<?php

declare(strict_types=1);

namespace App\Infrastructure\ErrorHandler;

use App\Infrastructure\Logging\Logger;
use Throwable;

final class ErrorHandler
{
    /**
     * Регистрирует обработчики ошибок и исключений
     */
    public static function register(): void
    {
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    /**
     * Обрабатывает PHP ошибки
     */
    public static function handleError(int $severity, string $message, string $file, int $line): bool
    {
        if (!(error_reporting() & $severity)) {
            return false;
        }

        $logger = Logger::getInstance();
        $logger->error('PHP Error', [
            'severity' => $severity,
            'message' => $message,
            'file' => $file,
            'line' => $line,
        ]);

        self::sendErrorResponse();
        return true;
    }

    /**
     * Обрабатывает необработанные исключения
     */
    public static function handleException(Throwable $exception): void
    {
        $logger = Logger::getInstance();
        $logger->error('Uncaught Exception', [
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ]);

        self::sendErrorResponse();
    }

    /**
     * Обрабатывает фатальные ошибки при завершении работы скрипта
     */
    public static function handleShutdown(): void
    {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE], true)) {
            $logger = Logger::getInstance();
            $logger->error('Fatal Error', [
                'message' => $error['message'],
                'file' => $error['file'],
                'line' => $error['line'],
            ]);

            self::sendErrorResponse();
        }
    }

    /**
     * Отправляет JSON ответ с ошибкой и завершает выполнение скрипта
     */
    private static function sendErrorResponse(): void
    {
        http_response_code(500);
        header('Content-Type: application/json');

        echo json_encode([
            'error' => 'Internal Server Error',
            'status' => 500,
        ], JSON_THROW_ON_ERROR);

        exit;
    }
}
