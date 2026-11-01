<?php

declare(strict_types=1);

namespace App\Controller\Http\Api\OrderNotification\Presenter;

use App\Domain\OrderNotification\Entity\NotificationTask;

class NotificationTaskPresenter
{
    public function toArray(NotificationTask $task): array
    {
        return [
            'taskId' => $task->getId()->value(),
            'taskStatus' => $task->getStatus()->value,
            'orderId' => $task->getOrderId()->value(),
            'orderEvent' => $task->getOrderEvent()->value,
            'channel' => $task->getChannel()->value,
            'recipient' => $task->getRecipient()->value(),
            'createdAt' => $task->getCreatedAt()->format(\DateTimeInterface::ATOM),
            'processedAt' => $task->getProcessedAt()?->format(\DateTimeInterface::ATOM),
        ];
    }
}
