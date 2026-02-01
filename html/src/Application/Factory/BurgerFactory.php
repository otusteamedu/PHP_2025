<?php

declare(strict_types=1);

namespace Otus\Food\Application\Factory;

use Otus\Food\Domain\Kitchen\Entity\Burger;

final class BurgerFactory extends AbstractFactory
{
    /**
     * @return Burger
     */
    public static function factory(): Burger
    {
        return new Burger();
    }
}
