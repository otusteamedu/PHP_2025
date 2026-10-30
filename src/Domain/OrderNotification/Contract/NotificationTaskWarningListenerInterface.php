<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\Contract;

use App\Domain\OrderNotification\Entity\NotificationTask;
use App\Domain\OrderNotification\Exception\Task\NotificationTaskStorageException;

/**
 * Сообщает о фактах, когда службе не удалось зафиксировать отметку статуса
 * задачи в хранилище (NotificationTaskStorageException). Возникает в момент
 * обработки уже свершившегося сбоя, поэтому реализация обязана не бросать
 * исключений: вызов происходит внутри перехвата, и исходная причина сбоя
 * важнее любой ошибки самого слушателя.
 */
interface NotificationTaskWarningListenerInterface
{
    /**
     * Служба не смогла зафиксировать статус Retrying после retryable-сбоя.
     */
    public function onRetryPersistFailure(NotificationTask $task, NotificationTaskStorageException $e): void;

    /**
     * Служба не смогла зафиксировать статус Failed при терминальном сбое.
     */
    public function onTerminalMarkFailure(NotificationTask $task, NotificationTaskStorageException $e): void;
}
