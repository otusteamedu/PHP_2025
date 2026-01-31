<?php

declare(strict_types=1);

namespace Otus\Food\Application\Factory;

use Otus\Food\Domain\Kitchen\Entity\Meal;

abstract class AbstractFactory
{
    /**
     * @return Meal
     */
    abstract public static function factory(): Meal;
}
