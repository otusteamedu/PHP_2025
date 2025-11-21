<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Service\Order\OrderHandler;



use Dinargab\Homework15\Model\Order\OrderStatus;

class CompletedOrderHandler extends AbstractOrderHandler
{
    public function handle($order): void
    {
        $order->setStatus(OrderStatus::COMPLETED);

        parent::handle($order);
    }
}