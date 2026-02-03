<?php

declare(strict_types=1);

namespace App\Domain\Order\Pipeline;

use App\Domain\Cooking\Factory\CookerFactory;
use App\Domain\EventDispatcher;
use App\Domain\Order\Order;
use App\Domain\Order\OrderStatus;
use App\Domain\Order\OrderStatusChangedEvent;

class CookOrderHandler extends OrderHandler
{
    public function __construct(
        private readonly CookerFactory $cookerFactory,
        private readonly EventDispatcher $dispatcher,
    ) {
    }

    protected function process(Order $order): void
    {
        foreach ($order->getItems() as $item) {
            $cooker = $this->cookerFactory->create($item->getProduct()->getBaseProduct());
            $cooker->cook($item->getProduct());
        }

        $newStatus = OrderStatus::Cooking;
        $order->setStatus($newStatus);

        $this->dispatcher->dispatch(
            new OrderStatusChangedEvent($order, $newStatus)
        );
    }
}
