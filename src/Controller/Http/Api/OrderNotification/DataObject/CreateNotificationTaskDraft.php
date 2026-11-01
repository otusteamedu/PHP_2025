<?php

declare(strict_types=1);

namespace App\Controller\Http\Api\OrderNotification\DataObject;

use App\Domain\OrderNotification\Command\CreateNotificationTaskCommand;
use App\Domain\OrderNotification\ValueObject\Order\OrderEvent;
use App\Domain\OrderNotification\ValueObject\Order\OrderId;
use App\Domain\OrderNotification\ValueObject\Recipient\RecipientInterface;
use App\Domain\OrderNotification\ValueObject\Task\NotificationChannel;

class CreateNotificationTaskDraft
{
    public ?OrderId $orderId = null;
    public ?OrderEvent $orderEvent = null;
    public ?NotificationChannel $channel = null;
    public ?RecipientInterface $recipient = null;

    public function toCommand(): CreateNotificationTaskCommand
    {
        if (
            $this->orderId === null
            || $this->orderEvent === null
            || $this->channel === null
            || $this->recipient === null
        ) {
            // Сюда можно попасть только если правило не записало значение,
            // но не добавило ошибку - это баг программиста, а не клиента.
            throw new \LogicException('Валидация прошла успешно, но черновик заполнен не полностью.');
        }

        return new CreateNotificationTaskCommand(
            $this->orderId,
            $this->orderEvent,
            $this->channel,
            $this->recipient,
        );
    }
}
