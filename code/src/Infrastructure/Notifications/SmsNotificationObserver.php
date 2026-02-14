<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifications;

use App\Domain\Interfaces\OrderObserverInterface;
use App\Domain\Entities\Order;

class SmsNotificationObserver implements OrderObserverInterface
{
    private array $sentNotifications = [];

    public function update(Order $order, string $event): void
    {
        $message = $this->buildMessage($order, $event);
        $this->sendSms($order->getId(), $message);

        $this->sentNotifications[] = [
            'type' => 'sms',
            'order_id' => $order->getId(),
            'event' => $event,
            'status' => $order->getStatus()->value,
            'message' => $message,
        ];
    }

    public function getSentNotifications(): array
    {
        return $this->sentNotifications;
    }

    private function buildMessage(Order $order, string $event): string
    {
        return sprintf(
            '[SMS] Заказ %s: %s - %s',
            $order->getId(),
            $event,
            $order->getStatus()->getDescription()
        );
    }

    private function sendSms(string $orderId, string $message): void
    {
        error_log("[SMS] Order: {$orderId} - {$message}");
    }
}
