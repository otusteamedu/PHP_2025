<?php
declare(strict_types=1);

namespace App\Repository;

use PDO;

class ReportRepository
{
    public function __construct(private PDO $pdo) {}

    public function create(): int
    {
        $stmt = $this->pdo->prepare("INSERT INTO reports (status) VALUES ('pending')");
        $stmt->execute();
        return (int)$this->pdo->lastInsertId();
    }

    public function getStatus(int $id): ?string
    {
        $stmt = $this->pdo->prepare("SELECT status FROM reports WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $status = $stmt->fetchColumn();
        return $status ?: null;
    }

    public function markCompleted(int $id): void
    {
        $stmt = $this->pdo->prepare("UPDATE reports SET status = 'done' WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }
}
