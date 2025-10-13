<?php
namespace App\Repository;

use App\Infrastructure\Database;
use PDO;

class TaskRepository
{
    private PDO $pdo;

    public function __construct(Database $db)
    {
        $this->pdo = $db->pdo();
    }

    public function create(string $requestId, ?string $payload = null): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO tasks (id, payload, status) VALUES (:id, :payload, :status) ON CONFLICT (id) DO NOTHING');
        $stmt->execute([
            ':id' => $requestId,
            ':payload' => $payload,
            ':status' => 'queued',
        ]);
    }

    public function markProcessed(string $requestId): void
    {
        $stmt = $this->pdo->prepare('UPDATE tasks SET status = :status, processed_at = NOW() WHERE id = :id');
        $stmt->execute([
            ':id' => $requestId,
            ':status' => 'processed',
        ]);
    }

    public function get(string $requestId): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, payload, status, processed_at FROM tasks WHERE id = :id');
        $stmt->execute([':id' => $requestId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
