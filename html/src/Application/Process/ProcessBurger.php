<?php

declare(strict_types=1);

namespace Otus\Food\Application\Process;

use Otus\Food\Application\Builder\Burger;
use Otus\Food\Application\Builder\Proxy;
use Otus\Food\Application\Factory\BurgerFactory;
use Otus\Food\Application\Observer\Kitchen;
use Otus\Food\Application\Observer\Monitor;
use Otus\Food\Domain\Kitchen\Entity\Meal;
use Otus\Food\Domain\Order\Order;

final readonly class ProcessBurger implements ProcessInterface
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
    public function run(): Meal
    {
        $meal = BurgerFactory::factory();

        $builder = new Burger($meal, $this->order);

        $kitchen = new Kitchen();
        $kitchen->attach(new Monitor());

        $proxy = new Proxy($builder, $kitchen);

        return $proxy->cooking();
    }
}
