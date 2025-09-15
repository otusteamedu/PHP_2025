<?php declare(strict_types=1);

namespace Fastfood\Orders\Observers;

use Fastfood\Orders\Order;

interface OrderObserverInterface
{
    public function update(Order $order, string $event): void;
}