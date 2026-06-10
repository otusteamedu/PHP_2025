<?php

declare(strict_types=1);

namespace MkdBot\Infrastructure\Queue;

use MkdBot\Application\UseCase\ProcessRagQuery;
use MkdBot\Domain\Interface\DatabaseConnectionInterface;
use MkdBot\Domain\Interface\FallbackMessageRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Consumer RAG-запросов — обрабатывает сообщения из mkd.rag.query
 * v1: заглушка — сразу ACK-ает сообщения с логированием
 */
class RagQueryConsumer extends RabbitMQConsumer
{
    public function __construct(
        RabbitMQConnectionFactory $connectionFactory,
        FallbackMessageRepositoryInterface $fallbackRepo,
        LoggerInterface $logger,
        private readonly ProcessRagQuery $processRagQuery,
        ?DatabaseConnectionInterface $dbConnection = null,
    ) {
        parent::__construct($connectionFactory, 'mkd.rag.query', $fallbackRepo, $logger, $dbConnection);
    }

    protected function processMessage(string $body, array $headers): void
    {
        $data = json_decode($body, true);
        if ($data === null) {
            $this->logger->warning("Не удалось декодировать JSON RAG-запроса: " . $body);
            return;
        }

        $userId = (int)($data['user_id'] ?? 0);
        $question = $data['question'] ?? '';

        $this->logger->info("v1: RAG-запрос — заглушка, сообщение пропущено. userId={$userId}");

        $this->processRagQuery->execute($userId, $question);
    }
}
