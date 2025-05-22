<?php
namespace App\ChainOfResponsibility;

use App\Order\OrderInterface;

class PaymentHandler implements OrderHandlerInterface
{
    private ?OrderHandlerInterface $nextHandler = null;

    public function setNext(OrderHandlerInterface $handler): void
    {
        $this->nextHandler = $handler;
    }

    public function handle(OrderInterface $order): void
    {
        echo "Обрабатываем оплату...\n";
        $order->setStatus('оплачен');
        
        if ($this->nextHandler) {
            $this->nextHandler->handle($order);
        }
    }
}
