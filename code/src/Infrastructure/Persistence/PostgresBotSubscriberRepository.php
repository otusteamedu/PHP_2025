<?php

declare(strict_types=1);

namespace MkdBot\Infrastructure\Persistence;

use DateTimeImmutable;
use MkdBot\Domain\Entity\BotSubscriber;
use MkdBot\Domain\Interface\BotSubscriberRepositoryInterface;
use MkdBot\Domain\Interface\DatabaseConnectionInterface;
use PDO;

/**
 * Репозиторий подписчиков бота — реализация через Postgres
 */
class PostgresBotSubscriberRepository implements BotSubscriberRepositoryInterface
{
    public function __construct(
        private readonly DatabaseConnectionInterface $db,
    ) {
    }

    public function findActive(): array
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->query("SELECT * FROM bot_subscribers WHERE is_active = TRUE ORDER BY subscribed_at ASC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn ($row) => $this->hydrate($row), $rows);
    }

    public function findByUserId(int $userId): ?BotSubscriber
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM bot_subscribers WHERE user_id = ?");
        $stmt->execute([$userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        return $this->hydrate($row);
    }

    public function save(BotSubscriber $subscriber): BotSubscriber
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare(
            "INSERT INTO bot_subscribers (user_id, user_name, subscribed_at, is_active, unsubscribed_at)
            VALUES (?, ?, ?, ?, ?)
            ON CONFLICT (user_id) DO UPDATE SET user_name = EXCLUDED.user_name, is_active = EXCLUDED.is_active, subscribed_at = EXCLUDED.subscribed_at, unsubscribed_at = EXCLUDED.unsubscribed_at",
        );
        $stmt->execute([
            $subscriber->getUserId(),
            $subscriber->getUserName(),
            $subscriber->getSubscribedAt()->format('Y-m-d H:i:s'),
            $subscriber->isActive(),
            $subscriber->getUnsubscribedAt()?->format('Y-m-d H:i:s'),
        ]);

        return $subscriber;
    }

    public function markAsInactive(int $userId): void
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("UPDATE bot_subscribers SET is_active = FALSE, unsubscribed_at = NOW() WHERE user_id = ?");
        $stmt->execute([$userId]);
    }

    public function markAsActive(int $userId): void
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("UPDATE bot_subscribers SET is_active = TRUE, subscribed_at = NOW(), unsubscribed_at = NULL WHERE user_id = ?");
        $stmt->execute([$userId]);
    }

    /**
     * Гидратация строки БД в сущность BotSubscriber
     */
    private function hydrate(array $row): BotSubscriber
    {
        return new BotSubscriber(
            userId: (int)$row['user_id'],
            userName: $row['user_name'] ?? '',
            subscribedAt: new DateTimeImmutable($row['subscribed_at']),
            isActive: $this->castBool($row['is_active']),
            unsubscribedAt: isset($row['unsubscribed_at']) && $row['unsubscribed_at'] !== ''
                ? new DateTimeImmutable($row['unsubscribed_at'])
                : null,
        );
    }

    /**
     * Приведение PostgreSQL boolean к PHP bool ('t'/'f', '1'/'0', true/false)
     */
    private function castBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        if (is_string($value)) {
            return $value === 't' || $value === '1' || strtolower($value) === 'true';
        }
        return (bool)$value;
    }
}
