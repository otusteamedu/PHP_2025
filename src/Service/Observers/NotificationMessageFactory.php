<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Service\Observers;

use Dinargab\Homework15\Model\Order\DTO\ProductOrderNotificationDTO;
use Dinargab\Homework15\Model\Order\OrderStatus;

class NotificationMessageFactory
{
    public function createMessage(ProductOrderNotificationDTO $notificationDTO)
    {
        return match ($notificationDTO->orderStatus) {
            OrderStatus::NEW => "New order created: $notificationDTO->orderId",
            OrderStatus::COOKING => "Your order #$notificationDTO->orderId started cooking",
            OrderStatus::COMPLETED => "Your order #$notificationDTO->orderId cooking completed",
            OrderStatus::READY => "Your order #$notificationDTO->orderId is ready for pick up",
        };
    }
}