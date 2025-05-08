<?php
declare(strict_types=1);

namespace App\Food\Application\Service\FoodOrderStatusHandler;

use App\Food\Domain\Aggregate\Order\FoodOrder;

abstract class FoodOrderStatusHandler implements FoodOrderStatusHandlerInterface
{
    public function __construct(
        private ?FoodOrderStatusHandlerInterface $nextHandler = null
    )
    {
    }

    public function setNext(FoodOrderStatusHandlerInterface $nextHandler): FoodOrderStatusHandlerInterface
    {
        $this->nextHandler = $nextHandler;
        return $nextHandler;
    }

    public function handle(FoodOrder $order): void
    {
        $this->nextHandler?->handle($order);
    }

}