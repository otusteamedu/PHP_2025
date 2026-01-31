<?php

declare(strict_types=1);

namespace Otus\Food\Domain\Kitchen\Entity;

abstract class Meal
{
    /**
     * @return string
     */
    abstract public function getTitle(): string;

    /**
     * @return array
     */
    abstract public function getRecept(): array;

    /**
     * @return Meal
     */
    public function clone(): self
    {
        return clone $this;
    }
}
