<?php

declare(strict_types=1);

namespace MkdBot\Infrastructure\Persistence;

use MkdBot\Domain\Interface\DatabaseConnectionInterface;
use MkdBot\Domain\Interface\ProcessedWebhookRepositoryInterface;

/**
 * Репозиторий идемпотентности webhook — реализация через Postgres
 * Атомарная проверка+запись через INSERT ... ON CONFLICT DO NOTHING
 */
class PostgresProcessedWebhookRepository implements ProcessedWebhookRepositoryInterface
{
    public function __construct(
        private readonly DatabaseConnectionInterface $db,
    ) {
    }

    public function tryAcquire(string $messageMid, string $messengerType): bool
    {
        $pdo = $this->db->getConnection();

        $stmt = $pdo->prepare(
            "INSERT INTO processed_webhooks (message_mid, messenger_type, created_at)
            VALUES (?, ?, NOW())
            ON CONFLICT (message_mid, messenger_type) DO NOTHING",
        );
        $stmt->execute([$messageMid, $messengerType]);

        return $stmt->rowCount() > 0;
    }
}
