<?php declare(strict_types=1);

namespace Fastfood\Orders\Observers;

use Fastfood\Orders\Order;

class SmsNotifier implements OrderObserverInterface
{
    public function update(Order $order, string $event): void
    {
        $messages = [
            'order_accepted' => "Заказ #{$order->getId()} принят в обработку",
            'order_preparing' => "Заказ #{$order->getId()} начали готовить",
            'order_ready' => "Заказ #{$order->getId()} готов!",
            'order_ready_for_pickup' => "Заказ #{$order->getId()} готов к выдаче!",
            'order_completed' => "Заказ #{$order->getId()} завершен"
        ];

        if (isset($messages[$event])) {
            error_log("SMS: {$messages[$event]}");
        }
    }
}