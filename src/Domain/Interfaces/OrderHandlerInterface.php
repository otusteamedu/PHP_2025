<?php

namespace Restaurant\Domain\Interfaces;

use Restaurant\Domain\Entities\Order;

interface OrderHandlerInterface
{
    public function setNext(OrderHandlerInterface $nextHandler): OrderHandlerInterface;

    public function handle(Order $order): bool;
}
