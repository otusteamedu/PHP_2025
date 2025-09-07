<?php

namespace App\Infrastructure\Repositories;

use \PDO;

class TodoRepository
{
    public function __construct(private PDO $pdo) {}

    public function getByUserId(int $userId): array
    {
        $sql = "SELECT id, user_id, content, created_at FROM todos WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        $todos = $stmt->fetchAll();
    
        return $todos;
    }

    public function getById(int $todoId, int $userId): ?array
    {
        $sql = "SELECT id, user_id, content, created_at FROM todos WHERE id = :id AND user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $todoId, 'user_id' => $userId]);
        $todo = $stmt->fetch();
        
        if (empty($todo)) {
            return null;
        }

        return $todo;
    }

    public function add(string $content, int $userId): void
    {
        $sql = "INSERT INTO todos (content, user_id) 
                VALUES (:content, :user_id)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'content' => $content,
            'user_id' => $userId,
        ]);
    }

    public function update(string $content, int $todoId, int $userId): void
    {
        $sql = "UPDATE todos 
            SET content = :content
            WHERE id = :id AND user_id = :user_id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'content' => $content,
            'id' => $todoId,
            'user_id' => $userId,
        ]);
    }

    public function delete(int $todoId, int $userId): void
    {
        $sql = "DELETE FROM todos WHERE id = :id AND user_id = :user_id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $todoId,
            'user_id' => $userId,
        ]);
    }
}