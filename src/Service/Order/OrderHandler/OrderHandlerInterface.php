<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Service\Order\OrderHandler;


use Dinargab\Homework15\Model\Order\ProductOrder;

interface OrderHandlerInterface
{
    public function setNext(OrderHandlerInterface $next): OrderHandlerInterface;

    public function handle(ProductOrder $order): void;
}