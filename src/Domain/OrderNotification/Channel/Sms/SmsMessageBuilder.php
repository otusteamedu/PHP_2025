<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\Channel\Sms;

use App\Domain\OrderNotification\Entity\NotificationTask;
use App\Domain\OrderNotification\ValueObject\Message\SmsMessage;
use App\Domain\OrderNotification\ValueObject\Order\OrderEvent;

class SmsMessageBuilder
{
    public function build(NotificationTask $task): SmsMessage
    {
        $orderId = $task->getOrderId()->value();
        $order = $task->requireOrderData();

        $text = match ($task->getOrderEvent()) {
            OrderEvent::Created => sprintf(
                'Заказ № %d создан. Товаров: %d. Сумма: %s.',
                $orderId,
                $order->getItemsCount(),
                $order->getTotal()->format(),
            ),
            OrderEvent::Paid => sprintf(
                'Заказ № %d оплачен. Сумма: %s.',
                $orderId,
                $order->getTotal()->format(),
            ),
            OrderEvent::Shipped => sprintf(
                'Заказ № %d отправлен. Трек: %s.',
                $orderId,
                $order->requireTrackingNumber(),
            ),
        };

        return new SmsMessage($text);
    }
}
