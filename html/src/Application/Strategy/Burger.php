<?php

declare(strict_types=1);

namespace Otus\Food\Application\Strategy;

use Otus\Food\Application\Process\ProcessBurger;
use Otus\Food\Application\UseCase\CookingBurger;
use Otus\Food\Domain\Kitchen\Entity\Meal;
use Otus\Food\Domain\Order\Order;

final class Burger implements MealStrategy
{
    /**
     * @param Order $order
     *
     * @return Meal
     */
    public function cooking(Order $order): Meal
    {
        return new CookingBurger(new ProcessBurger($order))->cooking();
    }
}
