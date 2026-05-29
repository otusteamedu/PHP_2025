<?php

declare(strict_types=1);

namespace MkdBot\Infrastructure\Persistence;

use DateTimeImmutable;
use MkdBot\Domain\Entity\NewsDelivery;
use MkdBot\Domain\Enum\NewsDeliveryStatus;
use MkdBot\Domain\Interface\DatabaseConnectionInterface;
use MkdBot\Domain\Interface\NewsDeliveryRepositoryInterface;
use PDO;

/**
 * Репозиторий доставок новостей — реализация через Postgres
 */
class PostgresNewsDeliveryRepository implements NewsDeliveryRepositoryInterface
{
    public function __construct(
        private readonly DatabaseConnectionInterface $db,
    ) {
    }

    public function findPendingByNewsId(int $newsId): array
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM news_deliveries WHERE news_id = ? AND status = 'pending'");
        $stmt->execute([$newsId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn ($row) => $this->hydrate($row), $rows);
    }

    public function findByNewsIdAndUserId(int $newsId, int $userId): ?NewsDelivery
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM news_deliveries WHERE news_id = ? AND user_id = ?");
        $stmt->execute([$newsId, $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        return $this->hydrate($row);
    }

    public function save(NewsDelivery $delivery): NewsDelivery
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare(
            "INSERT INTO news_deliveries (news_id, user_id, status, delivered_at)
            VALUES (?, ?, ?, ?)
            ON CONFLICT (news_id, user_id) DO UPDATE SET status = EXCLUDED.status, delivered_at = EXCLUDED.delivered_at",
        );
        $stmt->execute([
            $delivery->getNewsId(),
            $delivery->getUserId(),
            $delivery->getStatus()->value,
            $delivery->getDeliveredAt()?->format('Y-m-d H:i:s'),
        ]);

        return $delivery;
    }

    public function markAsSent(int $newsId, int $userId): void
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare(
            "UPDATE news_deliveries SET status = 'sent', delivered_at = NOW() WHERE news_id = ? AND user_id = ?",
        );
        $stmt->execute([$newsId, $userId]);
    }

    public function markAsFailed(int $newsId, int $userId): void
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare(
            "UPDATE news_deliveries SET status = 'failed' WHERE news_id = ? AND user_id = ?",
        );
        $stmt->execute([$newsId, $userId]);
    }

    public function findExistingDeliveryUserIds(int $newsId): array
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("SELECT user_id FROM news_deliveries WHERE news_id = ?");
        $stmt->execute([$newsId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    /**
     * Гидратация строки БД в сущность NewsDelivery
     */
    private function hydrate(array $row): NewsDelivery
    {
        return new NewsDelivery(
            newsId: (int)$row['news_id'],
            userId: (int)$row['user_id'],
            status: NewsDeliveryStatus::from((string)$row['status']),
            deliveredAt: $row['delivered_at'] !== null ? new DateTimeImmutable($row['delivered_at']) : null,
        );
    }
}
