<?php

declare(strict_types=1);

namespace Otus\Code\Domain\Order\Observer;

use Otus\Code\Domain\Order\Entity\Order;
use Otus\Code\Domain\Order\Event\OrderEvent;

interface OrderObserverInterface
{
    public function update(Order $order, OrderEvent $event): void;
}
