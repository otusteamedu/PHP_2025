<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Repository;

use App\Core\Database\Exception\DatabaseException;
use App\Core\Database\Repository\AbstractRepository;
use App\Domain\OrderNotification\Contract\NotificationTaskRepositoryInterface;
use App\Domain\OrderNotification\Entity\NotificationTask;
use App\Domain\OrderNotification\Exception\Task\NotificationTaskStorageException;
use App\Domain\OrderNotification\ValueObject\Task\TaskStatus;

class NotificationTaskRepository extends AbstractRepository implements NotificationTaskRepositoryInterface
{
    public function find(int $id): ?NotificationTask
    {
        try {
            return parent::find($id);
        } catch (DatabaseException $e) {
            throw NotificationTaskStorageException::loadFailed($id, $e);
        }
    }

    public function persist(NotificationTask $task): void
    {
        $this->persistSafely($task);
    }

    public function transitionTo(NotificationTask $task, TaskStatus $status, ?string $reason = null): void
    {
        $this->persistSafely($task, static fn() => $task->transitionTo($status, $reason));
    }

    public function restart(NotificationTask $task): void
    {
        $this->persistSafely($task, static fn() => $task->restart());
    }

    public function fail(NotificationTask $task, string $reason): void
    {
        $this->persistSafely($task, static fn() => $task->fail($reason));
    }

    protected function getTableName(): string
    {
        return 'notification_tasks';
    }

    protected function getSequenceName(): string
    {
        return 'notification_tasks_id_seq';
    }

    /**
     * @throws NotificationTaskStorageException
     */
    private function persistSafely(NotificationTask $task, ?\Closure $mutation = null): void
    {
        if ($mutation !== null) {
            $mutation();
        }

        try {
            $this->save($task);
        } catch (\Throwable $e) {
            $taskId = $task->getId()?->value();
            throw NotificationTaskStorageException::saveFailed($taskId, $e);
        }
    }
}
