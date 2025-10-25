<?php

declare(strict_types=1);

namespace Camal\AppDataMapperLocal\Infrastructure;

use Camal\AppDataMapperLocal\Domain\Entities\Task;
use Camal\AppDataMapperLocal\Domain\Entities\TaskPriority;
use Camal\AppDataMapperLocal\Domain\Entities\TaskStatus;
use Camal\AppDataMapperLocal\Domain\Entities\TaskDateTime;

use Pdo;

class TaskMapper
{
    public function __construct(private Pdo $pdo) {}

    /**
     * Найти задачи в базе данных
     * @param int $limit   Максимальное количество элементов, которые нужно вернуть. По умолчанию 12
     * @param int $offset  Количество элементов, которые нужно пропустить перед началом возврата результатов. 
     *                     Используется для пагинации.
     * @return array<Task> Массив экземпляров класса Task, соответствующих заданным параметрам, или пустой массив
     */
    public function findAll(int $limit, int $offset): array
    {

        $statement = $this->pdo->prepare(
            "SELECT
                        `taskId`,
                        `taskTitle`,
                        `taskText`,
                        `priorityCode`,
                        `priorityTitle`,
                        `statusCode`,
                        `statusTitle`,
                        `taskCreated`,
                        `taskCompleted`,
                        `taskCompleteBefore`
                    FROM
                        `tasks`
                    JOIN (
                        SELECT
                            `taskId` AS `id`
                        FROM
                            `tasks`
                        ORDER BY
                            `taskCreated` DESC
                        LIMIT
                            ?, ?
                    ) AS `t` ON `t`.`id` = `tasks`.`taskId`
                        JOIN `priorites` ON `priorites`.`priorityCode` = `tasks`.`taskPriority`
                        JOIN `statuses` ON `statuses`.`statusCode` = `tasks`.`taskStatus`;"
        );

        $statement->execute([$offset, $limit]);
        $raw = $statement->fetchAll(PDO::FETCH_ASSOC);
        $tasks = [];

        foreach ($raw as $item) {
            $tasks[] = new Task(
                $item["taskId"],
                $item["taskTitle"],
                $item["taskText"],
                new TaskPriority($item["priorityCode"]),
                new TaskStatus($item["statusCode"]),
                new TaskDateTime($item["taskCreated"]),
                isset($item["taskCompleted"])? new TaskDateTime($item["taskCompleted"]) : null,
                isset($item["taskCompleteBefore"])? new TaskDateTime($item["taskCompleteBefore"]): null
            );
        }
        return $tasks;
    }
    
    /**
     * Найти задачу по Id в базе данных
     * @param int $id
     * @return Task
     */
    public function findById(int $id): ?Task
    {
       $statement = $this->pdo->prepare(
            "SELECT
                        `taskId`,
                        `taskTitle`,
                        `taskText`,
                        `priorityCode`,
                        `priorityTitle`,
                        `statusCode`,
                        `statusTitle`,
                        `taskCreated`,
                        `taskCompleted`,
                        `taskCompleteBefore`
                    FROM
                        `tasks`
                        JOIN `priorites` ON `priorites`.`priorityCode` = `tasks`.`taskPriority`
                        JOIN `statuses` ON `statuses`.`statusCode` = `tasks`.`taskStatus`
                    WHERE
                        `taskId` = ?"
        );

        $statement->execute([$id]);

        $raw = $statement->fetch(PDO::FETCH_ASSOC);

        return new Task(
            $raw["taskId"],
            $raw["taskTitle"],
            $raw["taskText"],
            new TaskPriority($raw["priorityCode"]),
            new TaskStatus($raw["statusCode"]),
            new TaskDateTime($raw["taskCreated"]),
            isset($raw["taskCompleted"])? new TaskDateTime($raw["taskCompleted"]): null,
            isset($raw["taskCompleteBefore"])? new TaskDateTime($raw["taskCompleteBefore"]) : null
        );
    }

    /**
     * Создать новую задачу в базе данных
     * @param \Camal\AppDataMapperLocal\Domain\Entities\Task $task
     * @return bool
     */
    public function insert(Task $task): bool
    {
        $statement = $this->pdo->prepare(
            "INSERT INTO
                        `tasks`(
                            `taskTitle`,
                            `taskText`,
                            `taskPriority`,
                            `taskStatus`,
                            `taskCreated`
                        )
                    VALUES
                        (?, ?, ?, ?, ?);"
        );

        return $statement->execute([
            $task->getTitle(),
            $task->getText(),
            $task->getPriority()->getCode(),
            $task->getStatus()->getCode(),
            $task->getCreated()->toString()
        ]);
    }

    /**
     * Обновить обновляемые данные задачи в базе данных
     * @param \Camal\AppDataMapperLocal\Domain\Entities\Task $task
     * @return bool
     */
    public function update(Task $task): bool
    {
        $title = $task->getTitle();
        $text =    $task->getText();
        $priority =  $task->getPriority()->getCode();
        $status =   $task->getStatus()->getCode();
        $completed = ($task->getCompleted() !== null) ?  $task->getCompleted()->toString() : null;
        $completeBefore = ($task->getCompleteBefore() !== null) ? $task->getCompleteBefore()->toString() : null;
        $id =  $task->getId();

        $statement = $this->pdo->prepare(
            "UPDATE
                        `tasks`
                    SET
                        `taskTitle` = ?,
                        `taskText` = ?,
                        `taskPriority` = ?,
                        `taskStatus` = ?,
                        `taskCompleted` = ?,
                        `taskCompleteBefore` = ?
                    WHERE
                        `taskId` = ?;"
        );

        return $statement->execute([
            $title,
            $text,
            $priority,
            $status,
            $completed,
            $completeBefore,
            $id
        ]);
    }

    /**
     * Удалить задачу из базы данных
     * @param \Camal\AppDataMapperLocal\Domain\Entities\Task $task
     * @return bool
     */
    public function delete(int $id): bool
    {
        $statement = $this->pdo->prepare(
            "DELETE FROM
                        `tasks`
                    WHERE
                        `taskId` = ?;"
        );
        return $statement->execute([$id]);
    }
}
