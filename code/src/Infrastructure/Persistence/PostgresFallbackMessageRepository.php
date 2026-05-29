<?php

declare(strict_types=1);

namespace MkdBot\Infrastructure\Persistence;

use MkdBot\Domain\Entity\FallbackMessage;
use MkdBot\Domain\Interface\DatabaseConnectionInterface;
use MkdBot\Domain\Interface\FallbackMessageRepositoryInterface;

/**
 * Репозиторий неудачных сообщений из DLQ — реализация через Postgres
 */
class PostgresFallbackMessageRepository implements FallbackMessageRepositoryInterface
{
    public function __construct(
        private readonly DatabaseConnectionInterface $db,
    ) {
    }

    public function save(FallbackMessage $message): FallbackMessage
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare(
            "INSERT INTO fallback_messages (queue_name, message_body, error_message, x_death_count, created_at)
            VALUES (?, ?, ?, ?, ?)
            RETURNING id",
        );
        $stmt->execute([
            $message->getQueueName()->value,
            json_encode($message->getMessageBody(), JSON_UNESCAPED_UNICODE),
            $message->getErrorMessage(),
            $message->getXDeathCount(),
            $message->getCreatedAt()->format('Y-m-d H:i:s'),
        ]);

        $id = (int)$stmt->fetchColumn();

        return new FallbackMessage(
            id: $id,
            queueName: $message->getQueueName(),
            messageBody: $message->getMessageBody(),
            errorMessage: $message->getErrorMessage(),
            xDeathCount: $message->getXDeathCount(),
            createdAt: $message->getCreatedAt(),
        );
    }
}
