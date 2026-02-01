<?php

declare(strict_types=1);

namespace Otus\Food\Application\Builder;

use Otus\Food\Application\Observer\Kitchen;
use Otus\Food\Domain\Kitchen\Entity\Meal;

final class Proxy extends AbstractBuilder
{
    /**
     * @param AbstractBuilder $builder
     * @param Kitchen $kitchen
     */
    public function __construct(
        private readonly AbstractBuilder $builder,
        private readonly Kitchen $kitchen,
    ) {
        parent::__construct($this->builder->getMeal(), $this->builder->getOrder());
    }

    /**
     * @return Meal
     */
    public function cooking(): Meal
    {
        $this->kitchen->begin($this->builder)->notify();
        $meal = $this->builder->cooking();
        $this->kitchen->end($this->builder)->notify();

        return $meal;
    }
}
