<?php
namespace App\ChainOfResponsibility;

use App\Order\OrderInterface;

class DeliveryHandler implements OrderHandlerInterface
{
    private ?OrderHandlerInterface $nextHandler = null;

    public function setNext(OrderHandlerInterface $handler): void
    {
        $this->nextHandler = $handler;
    }

    public function handle(OrderInterface $order): void
    {
        echo "Доставляем заказ...\n";
        $order->setStatus('доставляется');
        sleep(3);
        $order->setStatus('доставлен');
        
        if ($this->nextHandler) {
            $this->nextHandler->handle($order);
        }
    }
}
