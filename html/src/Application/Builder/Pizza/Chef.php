<?php

declare(strict_types=1);

namespace Otus\Food\Application\Builder\Pizza;

use Otus\Food\Application\Builder\AbstractBuilder;
use Otus\Food\Domain\Kitchen\Entity\Meal;

final class Chef extends AbstractBuilder
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
                ->chicken()
                ->beef()
                ->beef()
                ->sauce()
                ->sauce()
                ->olive()
                ->olive()
                ->cheese()
                ->cheese();
    }
}
