<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Domain\Entity\Task;
use App\Domain\Enum\TaskStatus;
use App\Domain\Exception\AppException;
use DateTimeImmutable;
use PDO;

final readonly class TaskDataMapper
{
    public function __construct(
        private PDO $pdo,
    )
    {
    }

    public function insert(Task $task): void
    {
        $insertQuery = $this->pdo->prepare('

            insert into task
            (
                status,
                created_at
            )
            values
            (
                :status,
                :created_at
            )
            returning number
        ');

        $insertQuery->execute($this->toDatabaseRow($task));

        $task->assignNumber((int)$insertQuery->fetchColumn());
    }

    /**
     * @throws AppException
     */
    public function update(Task $task): void
    {
        if ($task->getNumber() === null) {
            throw new AppException(
                'Cannot update an task without an number.'
            );
        }

        $updateQuery = $this->pdo->prepare('
            update task
            set status = :status
            where number = :number
        ');

        $updateQuery->execute([
            'number' => $task->getNumber(),
            'status' => $task->getStatus()->value,
        ]);
    }

    public function findByNumber(int $number): ?Task
    {
        $query = $this->pdo->prepare('
            select *
            from task
            where number = :number
        ');

        $query->execute([
            'number' => $number,
        ]);

        $row = $query->fetch();

        if ($row === false) {
            return null;
        }

        return $this->createFromDatabaseRow($row);
    }

    /**
     * @throws AppException
     */
    public function delete(Task $task): void
    {
        if ($task->getNumber() === null) {
            throw new AppException(
                'Cannot delete an task without an number.'
            );
        }

        $deleteQuery = $this->pdo->prepare('
            delete
            from task
            where number = :number
        ');

        $deleteQuery->execute([
            'number' => $task->getNumber(),
        ]);
    }

    /**
     * @return Task[]
     * @throws \Exception
     */
    public function getNewTasks(int $limit): array
    {
        $selectQuery = $this->pdo->prepare('
            select *
            from task
            where status = :status
            order by number
            limit :limit
        ');

        $selectQuery->bindValue(
            'limit',
            $limit,
            PDO::PARAM_INT,
        );

        $selectQuery->bindValue(
            'status',
            TaskStatus::New->value,
        );

        $selectQuery->execute();

        $tasks = [];

        foreach ($selectQuery->fetchAll() as $databaseRow) {
            $tasks[] = $this->createFromDatabaseRow($databaseRow);
        }

        return $tasks;
    }

    private function toDatabaseRow(Task $task): array
    {
        return [
            'status' => $task->getStatus()->value,
            'created_at' => $task->getCreatedAt()->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * @throws \Exception
     */
    public function createFromDatabaseRow(array $row): Task
    {
        return new Task(
            number: (int)$row['number'],
            status: TaskStatus::from($row['status']),
            createdAt: new DateTimeImmutable($row['created_at']),
        );
    }
}