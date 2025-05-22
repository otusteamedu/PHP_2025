<?php
namespace App\ChainOfResponsibility;

use App\Order\OrderInterface;

class CookingHandler implements OrderHandlerInterface
{
    private ?OrderHandlerInterface $nextHandler = null;

    public function setNext(OrderHandlerInterface $handler): void
    {
        $this->nextHandler = $handler;
    }

    public function handle(OrderInterface $order): void
    {
        echo "Начинаем приготовление...\n";
        $order->setStatus('готовится');
        sleep(2);
        $order->setStatus('готов');
        
        if ($this->nextHandler) {
            $this->nextHandler->handle($order);
        }
    }
}
