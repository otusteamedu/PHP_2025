<?php
namespace App\ChainOfResponsibility;

use App\Order\OrderInterface;

class OrderProcessor
{
    private $handlers = [];

    public function addHandler(OrderHandlerInterface $handler): void
    {
        if (!empty($this->handlers)) {
            end($this->handlers)->setNext($handler);
        }
        $this->handlers[] = $handler;
    }

    public function processOrder(OrderInterface $order): void
    {
        if (!empty($this->handlers)) {
            $this->handlers[0]->handle($order);
        }
    }
}
