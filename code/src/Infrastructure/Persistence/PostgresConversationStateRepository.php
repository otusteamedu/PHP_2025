<?php

declare(strict_types=1);

namespace MkdBot\Infrastructure\Persistence;

use DateTimeImmutable;
use MkdBot\Domain\Entity\ConversationState;
use MkdBot\Domain\Enum\ConversationStep;
use MkdBot\Domain\Interface\ConversationStateRepositoryInterface;
use MkdBot\Domain\Interface\DatabaseConnectionInterface;

/**
 * Репозиторий состояний диалога — реализация через Postgres
 * user_id PRIMARY KEY — одна активная сессия на пользователя
 */
class PostgresConversationStateRepository implements ConversationStateRepositoryInterface
{
    public function __construct(
        private readonly DatabaseConnectionInterface $db,
    ) {
    }

    public function findByUserId(int $userId): ?ConversationState
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare(
            "SELECT user_id, current_step, data, updated_at, expires_at
             FROM conversation_states
             WHERE user_id = ?",
        );
        $stmt->execute([$userId]);
        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        return $this->hydrate($row);
    }

    public function save(ConversationState $state): ConversationState
    {
        $pdo = $this->db->getConnection();

        // Upsert — INSERT ... ON CONFLICT DO UPDATE (одна активная сессия на пользователя)
        $stmt = $pdo->prepare(
            "INSERT INTO conversation_states (user_id, current_step, data, updated_at, expires_at)
             VALUES (?, ?, ?, ?, ?)
             ON CONFLICT (user_id) DO UPDATE SET
                 current_step = EXCLUDED.current_step,
                 data = EXCLUDED.data,
                 updated_at = EXCLUDED.updated_at,
                 expires_at = EXCLUDED.expires_at",
        );
        $stmt->execute([
            $state->getUserId(),
            $state->getCurrentStep()->value,
            json_encode($state->getData(), JSON_UNESCAPED_UNICODE),
            $state->getUpdatedAt()->format('Y-m-d H:i:s'),
            $state->getExpiresAt()->format('Y-m-d H:i:s'),
        ]);

        return $state;
    }

    public function deleteByUserId(int $userId): bool
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("DELETE FROM conversation_states WHERE user_id = ?");
        $stmt->execute([$userId]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Гидратация строки БД в сущность ConversationState
     */
    private function hydrate(array $row): ConversationState
    {
        return new ConversationState(
            userId: (int)$row['user_id'],
            currentStep: ConversationStep::from($row['current_step']),
            data: json_decode($row['data'], true) ?? [],
            updatedAt: new DateTimeImmutable($row['updated_at']),
            expiresAt: new DateTimeImmutable($row['expires_at']),
        );
    }
}
