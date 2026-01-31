<?php

declare(strict_types=1);

namespace Otus\Food\Application\Builder;

use Otus\Food\Domain\Kitchen\Decorator\Beef;
use Otus\Food\Domain\Kitchen\Decorator\Cucumber;
use Otus\Food\Domain\Kitchen\Decorator\Ketchup;
use Otus\Food\Domain\Kitchen\Decorator\Lactuca;
use Otus\Food\Domain\Kitchen\Decorator\Mayonnaise;
use Otus\Food\Domain\Kitchen\Decorator\Tomato;

final class Burger extends AbstractBuilder
{
    /**
     * @return Ketchup
     */
    public function cooking(): Ketchup
    {
        return
            new Ketchup(
                new Mayonnaise(
                    new Cucumber(
                        new Tomato(
                            new Lactuca(
                                new Beef(
                                    $this->getMeal()
                                )
                            )
                        )
                    )
                )
            );
    }
}
