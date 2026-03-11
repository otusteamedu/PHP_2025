<?php
declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Task\Entity\Task;
use App\Domain\Task\Enum\TaskStatus;
use App\Domain\Task\Interface\TaskRepositoryInterface;
use App\Domain\Task\ValueObject\TaskId;
use App\Infrastructure\Database\PdoFactory;
use DateTimeImmutable;
use PDO;

class PostgresTaskRepository implements TaskRepositoryInterface
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? PdoFactory::create();
    }

    public function save(Task $task): void
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO tasks (id, status, payload, created_at, updated_at) VALUES (:id, :status, :payload, :created_at, :updated_at)'
        );

        $statement->execute([
            ':id' => $task->getId()->toString(),
            ':status' => $task->getStatus()->value,
            ':payload' => json_encode($task->getPayload(), JSON_THROW_ON_ERROR),
            ':created_at' => $task->getCreatedAt()->format(DATE_ATOM),
            ':updated_at' => $task->getUpdatedAt()->format(DATE_ATOM),
        ]);
    }

    public function findById(TaskId $id): ?Task
    {
        $statement = $this->pdo->prepare(
            'SELECT id, status, payload, created_at, updated_at FROM tasks WHERE id = :id LIMIT 1'
        );

        $statement->execute([':id' => $id->toString()]);
        $row = $statement->fetch();

        if ($row === false) {
            return null;
        }

        return new Task(
            id: new TaskId((string) $row['id']),
            status: TaskStatus::from((string) $row['status']),
            payload: $this->decodePayload($row['payload']),
            createdAt: new DateTimeImmutable((string) $row['created_at']),
            updatedAt: new DateTimeImmutable((string) $row['updated_at']),
        );
    }

    public function updateStatus(TaskId $id, TaskStatus $status): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE tasks SET status = :status, updated_at = NOW() WHERE id = :id'
        );


        $statement->execute([
            ':id' => $id->toString(),
            ':status' => $status->value,
        ]);
    }

    /**
     * @return array<string, mixed>
     * @throws \JsonException
     */
    private function decodePayload(mixed $payload): array
    {
        if (is_array($payload)) {
            return $payload;
        }

        if (is_string($payload) && $payload !== '') {
            $decoded = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);

            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }
}
