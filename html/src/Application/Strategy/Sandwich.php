<?php

declare(strict_types=1);

namespace Otus\Food\Application\Strategy;

use Otus\Food\Application\Process\ProcessSandwich;
use Otus\Food\Application\UseCase\CookingSandwich;
use Otus\Food\Domain\Kitchen\Entity\Meal;
use Otus\Food\Domain\Order\Order;

final class Sandwich implements MealStrategy
{
    /**
     * @param Order $order
     *
     * @return Meal
     */
    public function cooking(Order $order): Meal
    {
        return new CookingSandwich(new ProcessSandwich($order))->cooking();
    }
}
