<?php
namespace App\Observer;

use App\Order\OrderInterface;
use SplObserver;
use SplSubject;

class OrderStatusNotifier implements SplObserver
{
    public function update(SplSubject $order): void
    {
        echo "Статус заказа изменен: " . $order->getStatus() . "\n";
        
        if ($order->getStatus() === 'готов') {
            echo "Уведомление: Ваш заказ готов к выдаче!\n";
        }
    }
}
