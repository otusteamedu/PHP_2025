<?php

declare(strict_types=1);

namespace Otus\Food\Application\Builder;

use Otus\Food\Domain\Kitchen\Decorator\Chicken;
use Otus\Food\Domain\Kitchen\Decorator\Cucumber;
use Otus\Food\Domain\Kitchen\Decorator\Lactuca;
use Otus\Food\Domain\Kitchen\Decorator\Mayonnaise;
use Otus\Food\Domain\Kitchen\Decorator\Tomato;

final class Sandwich extends AbstractBuilder
{
    /**
     * @return Mayonnaise
     */
    public function cooking(): Mayonnaise
    {
        return
            new Mayonnaise(
                new Cucumber(
                    new Tomato(
                        new Lactuca(
                            new Chicken(
                                $this->getMeal()
                            )
                        )
                    )
                )
            );
    }
}
