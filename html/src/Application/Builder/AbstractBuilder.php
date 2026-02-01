<?php

declare(strict_types=1);

namespace Otus\Food\Application\Builder;

use Otus\Food\Domain\Kitchen\Entity\Meal;
use Otus\Food\Domain\Order\Order;

abstract class AbstractBuilder
{
    /**
     * @param Meal $meal
     * @param Order $order
     */
    public function __construct(
        private readonly Meal $meal,
        private readonly Order $order,
    ) {
    }

    /**
     * @return Meal
     */
    public function getMeal(): Meal
    {
        return $this->meal;
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @return Meal
     */
    abstract public function cooking(): Meal;
}
