<?php

declare(strict_types=1);

namespace App\Food\Application\Service\FoodOrderStatusValidator;

use App\Food\Application\Service\FoodOrderStatusHandler\FoodOrderStatusHandlerInterface;
use App\Food\Domain\Aggregate\Order\FoodOrder;

readonly class FoodOrderStatusValidator
{
    public function __construct(
        private FoodOrderStatusHandlerInterface $firstHandler,
    ) {
    }

    public function validate(FoodOrder $order): void
    {
        $this->firstHandler->handle($order);
    }
}
