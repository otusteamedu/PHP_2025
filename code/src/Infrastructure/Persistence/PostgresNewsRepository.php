<?php

declare(strict_types=1);

namespace MkdBot\Infrastructure\Persistence;

use DateTimeImmutable;
use MkdBot\Domain\Entity\News;
use MkdBot\Domain\Enum\NewsStatus;
use MkdBot\Domain\Interface\DatabaseConnectionInterface;
use MkdBot\Domain\Interface\NewsRepositoryInterface;
use PDO;

/**
 * Репозиторий новостей — реализация через Postgres
 */
class PostgresNewsRepository implements NewsRepositoryInterface
{
    public function __construct(
        private readonly DatabaseConnectionInterface $db,
    ) {
    }

    public function findById(int $id): ?News
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        return $this->hydrate($row);
    }

    public function findPending(): array
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->query("SELECT * FROM news WHERE status = 'pending' ORDER BY priority DESC, created_at ASC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn ($row) => $this->hydrate($row), $rows);
    }

    public function findDelivering(): array
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->query("SELECT * FROM news WHERE status = 'delivering' ORDER BY priority DESC, created_at ASC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn ($row) => $this->hydrate($row), $rows);
    }

    public function save(News $news): News
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare(
            "INSERT INTO news (title, content, priority, created_at, status)
            VALUES (?, ?, ?, ?, ?)
            RETURNING id",
        );
        $stmt->execute([
            $news->getTitle(),
            $news->getContent(),
            $news->getPriority(),
            $news->getCreatedAt()->format('Y-m-d H:i:s'),
            $news->getStatus()->value,
        ]);

        $id = (int)$stmt->fetchColumn();

        return new News(
            id: $id,
            title: $news->getTitle(),
            content: $news->getContent(),
            priority: $news->getPriority(),
            createdAt: $news->getCreatedAt(),
            status: $news->getStatus(),
        );
    }

    public function markAsDelivering(int $newsId): void
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("UPDATE news SET status = 'delivering' WHERE id = ?");
        $stmt->execute([$newsId]);
    }

    public function markAsDelivered(int $newsId): void
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("UPDATE news SET status = 'delivered' WHERE id = ?");
        $stmt->execute([$newsId]);
    }

    /**
     * Гидратация строки БД в сущность News
     */
    private function hydrate(array $row): News
    {
        return new News(
            id: (int)$row['id'],
            title: $row['title'],
            content: $row['content'],
            priority: (int)$row['priority'],
            createdAt: new DateTimeImmutable($row['created_at']),
            status: NewsStatus::from((string)$row['status']),
        );
    }
}
