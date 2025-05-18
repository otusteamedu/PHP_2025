<?php

namespace App\Domain\Entity\Order;

use App\Domain\Observer\Order\OrderInterface;

class OrderObserver implements OrderInterface  
{  

    public function __construct(  
        private $status
    )  
    {  
    }  

    public function notify(Order $order): void  
    {  
        echo "<p>Отправим оповещение Пользователю {$order->getUser()} следующего содержания: Статус вашего заказа {$order->getId()} изменился на {$order->getStatus()}</p>";
    }  
}  