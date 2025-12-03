<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Service\Order\OrderHandler;


use Dinargab\Homework15\Model\Order\OrderStatus;

class ReadyOrderHandler extends AbstractOrderHandler
{
    public function handle($order): void
    {
        $order->setStatus(OrderStatus::READY);
        parent::handle($order);
    }
}