<?php

declare(strict_types=1);

namespace App\Food\Application\Service\FoodOrderStatusHandler;

use App\Food\Domain\Aggregate\Order\FoodOrder;

interface FoodOrderStatusHandlerInterface
{
    public function setNext(FoodOrderStatusHandlerInterface $nextHandler): FoodOrderStatusHandlerInterface;

    public function handle(FoodOrder $order): void;
}
