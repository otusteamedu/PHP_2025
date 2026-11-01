<?php

declare(strict_types=1);

namespace App\Controller\Amqp\OrderNotification;

use App\Domain\OrderNotification\Contract\NotificationTaskWarningListenerInterface;
use App\Domain\OrderNotification\Entity\NotificationTask;
use App\Domain\OrderNotification\Exception\Task\NotificationTaskStorageException;
use App\Domain\OrderNotification\ValueObject\Task\TaskStatus;

class StderrNotificationTaskWarningListener implements NotificationTaskWarningListenerInterface
{
    public function onRetryPersistFailure(NotificationTask $task, NotificationTaskStorageException $e): void
    {
        $this->write('WARN', TaskStatus::Retrying, $task, $e);
    }

    public function onTerminalMarkFailure(NotificationTask $task, NotificationTaskStorageException $e): void
    {
        $this->write('FAIL-MARK', TaskStatus::Failed, $task, $e);
    }

    private function write(
        string $level,
        TaskStatus $status,
        NotificationTask $task,
        \Throwable $e,
    ): void {
        fwrite(STDERR, sprintf(
            " [%s] Failed to persist '%s' mark | Task: %d | Reason: %s\n",
            $level,
            $status->value,
            $task->getId()->value(),
            $e->getMessage(),
        ));
    }
}
