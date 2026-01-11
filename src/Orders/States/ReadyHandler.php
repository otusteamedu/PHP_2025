<?php declare(strict_types=1);

namespace Fastfood\Orders\States;

use Fastfood\Orders\Order;

class ReadyHandler extends OrderStateHandler
{
    public function process(Order $order): void
    {
        if ($order->getStatus() === 'ready') {
            $order->notify('order_ready_for_pickup');
            sleep(5);
        }

        $this->handle($order);
    }
}