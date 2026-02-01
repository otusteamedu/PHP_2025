<?php

declare(strict_types=1);

namespace Otus\Food\Application\Process;

use Otus\Food\Application\Builder\Proxy;
use Otus\Food\Application\Builder\Sandwich;
use Otus\Food\Application\Factory\SandwichFactory;
use Otus\Food\Application\Observer\Kitchen;
use Otus\Food\Application\Observer\Monitor;
use Otus\Food\Domain\Kitchen\Entity\Meal;
use Otus\Food\Domain\Order\Order;

final readonly class ProcessSandwich implements ProcessInterface
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
        $meal = SandwichFactory::factory();

        $builder = new Sandwich($meal, $this->order);

        $kitchen = new Kitchen();
        $kitchen->attach(new Monitor());

        $proxy = new Proxy($builder, $kitchen);

        return $proxy->cooking();
    }
}
