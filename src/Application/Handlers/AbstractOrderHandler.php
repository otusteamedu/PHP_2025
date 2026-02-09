<?php

namespace Restaurant\Application\Handlers;

use Restaurant\Domain\Interfaces\OrderHandlerInterface;
use Restaurant\Domain\Entities\Order;

abstract class AbstractOrderHandler implements OrderHandlerInterface
{
    private ?OrderHandlerInterface $nextHandler = null;

    public function __construct()
    {
    }

    public function setNext(OrderHandlerInterface $nextHandler): OrderHandlerInterface
    {
        $this->nextHandler = $nextHandler;
        return $nextHandler;
    }

    protected function handleNext(Order $order): bool
    {
        if ($this->nextHandler === null) {
            echo "Цепочка обработки заказа #{$order->getId()} завершена.\n";
            return true;
        }
        return $this->nextHandler->handle($order);
    }
}
