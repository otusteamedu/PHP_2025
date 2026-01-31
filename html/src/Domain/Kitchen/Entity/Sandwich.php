<?php

declare(strict_types=1);

namespace Otus\Food\Domain\Kitchen\Entity;

final class Sandwich extends Meal
{
    /**
     * @return string
     */
    public function getTitle(): string
    {
        return 'Sandwich';
    }

    /**
     * @return string[]
     */
    public function getRecept(): array
    {
        return ['toaster bread'];
    }
}
