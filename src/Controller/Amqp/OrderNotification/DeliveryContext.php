<?php

declare(strict_types=1);

namespace App\Controller\Amqp\OrderNotification;

use App\Domain\OrderNotification\Entity\NotificationTask;

/**
 * Контекст текущей доставки.
 *
 * Живёт в течение одной доставки сообщения; сбрасывается при старте следующей.
 * Поля start-набора заполняются один раз в конструкторе, error и task - по ходу обработки.
 */
class DeliveryContext
{
    private ?NotificationTask $task = null;
    private ?\Throwable $error = null;

    public function __construct(
        public readonly int $taskId,
        public readonly float $startedAt,
        public readonly float $retryAgeSeconds,
    ) {
    }

    public function attachTask(NotificationTask $task): void
    {
        if ($this->task !== null) {
            throw new \LogicException('Задача уже привязана к доставке.');
        }

        $this->task = $task;
    }

    /**
     * Задача, если она была найдена; null до привязки или если задача не нашлась.
     */
    public function getTask(): ?NotificationTask
    {
        return $this->task;
    }

    /**
     * Задача, которая обязана существовать в точке вызова; иначе - баг программиста.
     */
    public function requireTask(): NotificationTask
    {
        return $this->task ?? throw new \LogicException('Задача не привязана к доставке.');
    }

    public function getError(): ?\Throwable
    {
        return $this->error;
    }

    public function rememberError(\Throwable $e): void
    {
        $this->error = $e;
    }
}
