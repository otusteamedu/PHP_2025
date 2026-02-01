<?php

declare(strict_types=1);

namespace Otus\Food\Application\Factory;

use Otus\Food\Domain\Kitchen\Entity\Pizza;

final class PizzaFactory extends AbstractFactory
{
    /**
     * @return Pizza
     */
    public static function factory(): Pizza
    {
        return new Pizza();
    }
}
