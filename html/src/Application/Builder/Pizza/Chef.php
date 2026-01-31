<?php

declare(strict_types=1);

namespace Otus\Food\Application\Builder\Pizza;

use Otus\Food\Application\Builder\AbstractBuilder;
use Otus\Food\Domain\Kitchen\Decorator\Beef;
use Otus\Food\Domain\Kitchen\Decorator\Cheese;
use Otus\Food\Domain\Kitchen\Decorator\Chicken;
use Otus\Food\Domain\Kitchen\Decorator\Olive;
use Otus\Food\Domain\Kitchen\Decorator\Sauce;

final class Chef extends AbstractBuilder
{
    /**
     * @return Cheese
     */
    public function cooking(): Cheese
    {
        return
            new Cheese(
                new Cheese(
                    new Olive(
                        new Olive(
                            new Sauce(
                                new Sauce(
                                    new Beef(
                                        new Beef(
                                            new Chicken(
                                                new Chicken(
                                                    $this->getMeal()
                                                )
                                            )
                                        )
                                    )
                                )
                            )
                        )
                    )
                )
            );
    }
}
