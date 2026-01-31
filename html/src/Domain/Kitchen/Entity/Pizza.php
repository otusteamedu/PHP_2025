<?php

declare(strict_types=1);

namespace Otus\Food\Domain\Kitchen\Entity;

final class Pizza extends Meal
{
    /**
     * @return string
     */
    public function getTitle(): string
    {
        return 'Pizza';
    }

    /**
     * @return string[]
     */
    public function getRecept(): array
    {
        return ['focaccia'];
    }
}
