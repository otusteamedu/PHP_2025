<?php

declare(strict_types=1);

namespace Otus\Food\Application\Strategy;

use Otus\Food\Domain\Kitchen\Entity\Meal;
use Otus\Food\Domain\Order\Order;

interface MealStrategy
{
    /**
     * @param Order $order
     *
     * @return Meal
     */
    public function cooking(Order $order): Meal;
}
