<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Service\Order\OrderHandler;


use Dinargab\Homework15\Model\Order\ProductOrder;

class AbstractOrderHandler implements OrderHandlerInterface
{

    public function __construct(private ?OrderHandlerInterface $nextHandler = null)
    {

    }

    public function setNext(OrderHandlerInterface $next): OrderHandlerInterface
    {
        $this->nextHandler = $next;
        return $next;
    }

    public function handle(ProductOrder $order): void
    {
        if ($this->nextHandler !== null) {
            $this->nextHandler->handle($order);
        }
    }
}