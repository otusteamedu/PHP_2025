<?php declare(strict_types=1);

namespace Fastfood\Orders\States;

use Fastfood\Orders\Order;

class CompletedHandler extends OrderStateHandler
{
    public function process(Order $order): void
    {
        if ($order->getStatus() === 'completed') {
            $order->notify('order_completed');
        }

        $this->handle($order);
    }
}