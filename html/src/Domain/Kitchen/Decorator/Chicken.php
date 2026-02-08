<?php

declare(strict_types=1);

namespace Otus\Food\Domain\Kitchen\Decorator;

final class Chicken extends Ingredient
{
    /**
     * @return string[]
     */
    protected function action(): array
    {
        return ['chicken'];
    }
}
