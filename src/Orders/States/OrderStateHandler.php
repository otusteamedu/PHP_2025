<?php declare(strict_types=1);

namespace Fastfood\Orders\States;

use Fastfood\Orders\Order;

abstract class OrderStateHandler
{
    private ?OrderStateHandler $nextHandler = null;

    public function setNext(OrderStateHandler $handler): OrderStateHandler
    {
        $this->nextHandler = $handler;
        return $handler;
    }

    public function handle(Order $order): void
    {
        $this->nextHandler?->process($order);
    }

    abstract public function process(Order $order): void;
}