<?php

declare(strict_types=1);

namespace Otus\Food\Domain\Kitchen\Decorator;

final class Sauce extends Ingredient
{
    /**
     * @return string[]
     */
    protected function action(): array
    {
        return ['sauce'];
    }
}
