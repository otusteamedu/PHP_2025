<?php

declare(strict_types=1);

namespace App\Domain\Order\Pipeline;

use App\Domain\Order\Order;

abstract class OrderHandler
{
    private ?OrderHandler $next = null;

    public function setNext(OrderHandler $handler): OrderHandler
    {
        $this->next = $handler;

        return $handler;
    }

    final public function handle(Order $order): void
    {
        $this->process($order);

        if ($this->next) {
            $this->next->handle($order);
        }
    }

    abstract protected function process(Order $order): void;
}
