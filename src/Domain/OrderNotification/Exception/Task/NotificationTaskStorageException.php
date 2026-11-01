<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\Exception\Task;

use App\Domain\OrderNotification\Exception\RetryableFailure;
use App\Domain\Shared\Exception\ServiceUnavailableException;

class NotificationTaskStorageException extends ServiceUnavailableException implements RetryableFailure
{
    public static function saveFailed(?int $taskId, \Throwable $e): self
    {
        $taskId = ($taskId === null) ? '(новая)' : sprintf('№ %d', $taskId);

        return new self(sprintf('Не удалось сохранить задачу %s.', $taskId), previous: $e);
    }

    public static function loadFailed(int $taskId, \Throwable $e): self
    {
        return new self(sprintf('Не удалось загрузить задачу № %d.', $taskId), previous: $e);
    }
}
