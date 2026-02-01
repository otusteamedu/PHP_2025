<?php

declare(strict_types=1);

namespace Otus\Food\Application\Factory;

use Otus\Food\Domain\Kitchen\Entity\Sandwich;

final class SandwichFactory extends AbstractFactory
{
    /**
     * @return Sandwich
     */
    public static function factory(): Sandwich
    {
        return new Sandwich();
    }
}
