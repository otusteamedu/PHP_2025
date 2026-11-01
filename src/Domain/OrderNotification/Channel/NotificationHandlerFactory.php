<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\Channel;

use App\Domain\OrderNotification\ValueObject\Task\NotificationChannel;

class NotificationHandlerFactory
{
    /**
     * @param list<NotificationHandlerInterface> $handlers
     */
    public function __construct(
        private readonly array $handlers,
    ) {
    }

    public function getForChannel(NotificationChannel $channel): NotificationHandlerInterface
    {
        foreach ($this->handlers as $handler) {
            if ($handler->supports($channel)) {
                return $handler;
            }
        }

        throw new \LogicException("Нет обработчика для канала уведомлений: '$channel->value'");
    }
}
