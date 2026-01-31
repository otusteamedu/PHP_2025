<?php

declare(strict_types=1);

namespace Otus\Food\Application\UseCase\Pizza;

use Otus\Food\Application\Builder\Pizza\Chef;
use Otus\Food\Application\Builder\Proxy;
use Otus\Food\Application\Factory\PizzaFactory;
use Otus\Food\Application\Observer\Kitchen;
use Otus\Food\Application\Observer\Monitor;
use Otus\Food\Domain\Kitchen\Entity\Meal;
use Otus\Food\Domain\Order\Order;

final class CookingChef
{
    /**
     * @param Order $order
     */
    public function __construct(private Order $order)
    {
    }

    /**
     * @return Meal
     */
    public function cooking(): Meal
    {
        $meal = PizzaFactory::factory();

        $builder = new Chef($meal, $this->order);

        // todo
        $kitchen = new Kitchen();
        $kitchen->attach(new Monitor());

        $proxy = new Proxy($builder, $kitchen);

        return $proxy->cooking();
    }
}
