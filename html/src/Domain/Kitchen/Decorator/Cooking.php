<?php

declare(strict_types=1);

namespace Otus\Food\Domain\Kitchen\Decorator;

use Otus\Food\Domain\Kitchen\Entity\Meal;

abstract class Cooking extends Meal
{
    /**
     * @param Meal $meal
     */
    public function __construct(protected Meal $meal)
    {
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->meal->getTitle();
    }

    /**
     * @return array
     */
    public function getRecept(): array
    {
        return array_merge($this->meal->getRecept(), $this->action());
    }

    /**
     * @return array
     */
    abstract protected function action(): array;
}
