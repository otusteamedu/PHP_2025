<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\Channel\Email;

use App\Domain\OrderNotification\Entity\NotificationTask;
use App\Domain\OrderNotification\ValueObject\Message\EmailMessage;
use App\Domain\OrderNotification\ValueObject\Order\OrderEvent;

class EmailMessageBuilder
{
    public function build(NotificationTask $task): EmailMessage
    {
        $orderId = $task->getOrderId()->value();
        $order = $task->requireOrderData();

        return match ($task->getOrderEvent()) {
            OrderEvent::Created => new EmailMessage(
                subject: "Заказ № {$orderId} создан.",
                body: sprintf(
                    "Здравствуйте, %s!\n\nВаш заказ № %d создан и ожидает оплаты.\n"
                    . "Товаров: %d. Сумма: %s.",
                    $order->getCustomerName(),
                    $orderId,
                    $order->getItemsCount(),
                    $order->getTotal()->format(),
                ),
            ),
            OrderEvent::Paid => new EmailMessage(
                subject: "Оплата заказа № {$orderId}.",
                body: sprintf(
                    "Здравствуйте, %s!\n\nОплата заказа № %d на сумму %s подтверждена.\nСпасибо за покупку!",
                    $order->getCustomerName(),
                    $orderId,
                    $order->getTotal()->format(),
                ),
            ),
            OrderEvent::Shipped => new EmailMessage(
                subject: "Заказ № {$orderId} отправлен",
                body: sprintf(
                    "Здравствуйте, %s!\n\nВаш заказ № %d отправлен.\nТрек-номер: %s.",
                    $order->getCustomerName(),
                    $orderId,
                    $order->requireTrackingNumber(),
                ),
            ),
        };
    }
}
