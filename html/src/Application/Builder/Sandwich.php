<?php

declare(strict_types=1);

namespace Otus\Food\Application\Builder;

use Otus\Food\Domain\Kitchen\Entity\Meal;

final class Sandwich extends AbstractBuilder
{
    /**
     * @return Meal
     */
    public function cooking(): Meal
    {
        return
            $this
                ->getMeal()
                ->chicken()
                ->lactuca()
                ->tomato()
                ->cucumber()
                ->mayonnaise();
    }
}
