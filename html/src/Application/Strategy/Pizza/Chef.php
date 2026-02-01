<?php

declare(strict_types=1);

namespace Otus\Food\Application\Strategy\Pizza;

use Otus\Food\Application\Process\Pizza\ChefProcess;
use Otus\Food\Application\Strategy\MealStrategy;
use Otus\Food\Application\UseCase\Pizza\CookingChef;
use Otus\Food\Domain\Kitchen\Entity\Meal;
use Otus\Food\Domain\Order\Order;

final class Chef implements MealStrategy
{
    /**
     * @param Order $order
     *
     * @return Meal
     */
    public function cooking(Order $order): Meal
    {
        return new CookingChef(new ChefProcess($order))->cooking();
    }
}
