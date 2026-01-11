<?php declare(strict_types=1);

namespace Fastfood\Orders\Observers;

use Fastfood\Orders\Order;

class PushNotifier implements OrderObserverInterface
{
    private Order $order;
    public function update(Order $order, string $event): void
    {
        $this->order = $order;

        $messages = [
            'order_accepted' => "sendOrderAccepted",
            'order_preparing' => "sendOrderPreparing",
            'order_ready' => "sendOrderReady",
            'order_ready_for_pickup' => "sendReadyForPickup",
            'order_completed' => "sendCompleted"
        ];

        if ($name = $messages[$event]) {
            $this->$name();
        }
    }

    private function sendOrderAccepted(): void
    {
        echo "ID заказа: " . $this->order->getId() . "\n";
        echo "Общая стоимость: " . $this->order->getTotalCost() . " руб.\n";
        echo "Статус: " . $this->order->getStatus() . "\n";
        echo "=====================\n";
    }

    private function sendOrderPreparing(): void
    {
        echo "\nЗаказ #{$this->order->getId()} начали готовить\n";
    }

    private function sendOrderReady(): void
    {
        echo "\nЗаказ #{$this->order->getId()} готов!\n";
    }

    private function sendReadyForPickup(): void
    {
        echo "\nЗаказ #{$this->order->getId()} готов к выдаче!\n";
    }

    private function sendCompleted(): void
    {
        echo "\nЗаказ #{$this->order->getId()} завершен\n";
    }
}