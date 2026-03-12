<?php

declare(strict_types=1);

namespace Queues\Application\Handlers;

use Psr\Log\LoggerInterface;
use Queues\Application\Interfaces\ResponseInterface;

class ErrorHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ResponseInterface $response,
    ) {
    }

    public function handle(\Throwable $e): void
    {
        $this->logger->error($e->getMessage(), ['exception' => get_class($e)]);

        [$status, $message] = match (true) {
            $e instanceof \InvalidArgumentException => [400, $e->getMessage()],
            $e instanceof \RuntimeException => [503, 'Сервис временно недоступен'],
            default => [500, 'Внутренняя ошибка сервера'],
        };

        $this->response->withStatus($status);
        $this->response->json(['success' => false, 'error' => $message]);
    }
}
