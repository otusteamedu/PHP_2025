<?php

namespace App\Infrastructure\Repositories;

use \PDO;

class TaskRepository
{
    const STATUS_PENDING = 'pending';
    
    public function __construct(private PDO $pdo) {}

    public function add(int $userId): int
    {
        $sql = "INSERT INTO tasks (status, user_id) 
                VALUES (:status, :user_id)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'status' => self::STATUS_PENDING,
            'user_id' => $userId,
        ]);

        return $this->pdo->lastInsertId();
    }

    public function complete(int $taskId, int $userId): void
    {
        $sql = "UPDATE tasks
                SET status = 'success'
                WHERE id = :id AND user_id = :user_id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $taskId,
            'user_id' => $userId,
        ]);

    }

    public function getStatusById(int $taskId, int $userId): ?string
    {
        $sql = "SELECT status FROM tasks WHERE id = :id AND user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $taskId, 'user_id' => $userId]);
        $taskData = $stmt->fetch();
        
        if (empty($taskData) || empty($taskData['status'])) {
            return null;
        }

        return $taskData['status'];
    }
}