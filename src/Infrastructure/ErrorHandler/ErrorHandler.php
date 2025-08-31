<?php

declare(strict_types=1);

namespace App\Infrastructure\ErrorHandler;

use App\Infrastructure\Logging\Logger;
use Throwable;

final class ErrorHandler
{
    private \Monolog\Logger $logger;

    public function __construct()
    {
        $this->logger = Logger::getInstance();
    }

    /**
     * Обрабатывает исключение и возвращает JSON-ответ с ошибкой
     */
    public function handle(Throwable $exception): void
    {
        $this->logger->error('Unhandled exception: ' . $exception->getMessage(), [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]);

        http_response_code(500);
        echo json_encode([
            'error' => 'Internal server error',
            'message' => $exception->getMessage()
        ], JSON_THROW_ON_ERROR);
    }
}
