<?php

declare(strict_types=1);

namespace MkdBot\Application\UseCase;

use Psr\Log\LoggerInterface;

/**
 * Обработка RAG-запроса (v1 — заглушка)
 */
class ProcessRagQuery
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * v1: заглушка — логирует и пропускает
     */
    public function execute(int $userId, string $question): void
    {
        $this->logger->info("v1: RAG-запрос — заглушка, сообщение пропущено. userId={$userId}");
    }
}
