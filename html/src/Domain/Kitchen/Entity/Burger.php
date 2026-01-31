<?php

declare(strict_types=1);

namespace Otus\Food\Domain\Kitchen\Entity;

final class Burger extends Meal
{
    /**
     * @return string
     */
    public function getTitle(): string
    {
        return 'Burger';
    }

    /**
     * @return string[]
     */
    public function getRecept(): array
    {
        return ['bun'];
    }
}
