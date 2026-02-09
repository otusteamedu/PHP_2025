<?php

namespace Restaurant\Application\Services;

use Restaurant\Domain\Entities\Order;
use Restaurant\Domain\Interfaces\OrderHandlerInterface;

readonly class OrderProcessingService
{
    public function __construct(
        private readonly OrderHandlerInterface $handlerChain
    ) {
    }

    public function processOrder(Order $order): bool
    {
        echo "Начинаем обработку заказа #{$order->getId()} через цепочку обязанностей...\n";
        return $this->handlerChain->handle($order);
    }
}
