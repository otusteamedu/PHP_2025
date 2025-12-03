<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Service\Order\OrderHandler;


use Dinargab\Homework15\Model\Order\OrderStatus;
use Dinargab\Homework15\Model\Order\ProductOrder;

class NewOrderHandler extends AbstractOrderHandler
{
    public function __construct(
        private ?OrderHandlerInterface $orderHandler = null,
    )
    {
        parent::__construct($this->orderHandler);
    }

    public function handle(ProductOrder $order): void
    {
        $order->setStatus(OrderStatus::NEW);
        $this->startCookingProcess($order);
        parent::handle($order);
    }

    public function startCookingProcess(ProductOrder $order): void
    {
        echo "Started cooking process.\n";
    }
}