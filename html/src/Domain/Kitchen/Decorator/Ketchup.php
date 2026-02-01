<?php

declare(strict_types=1);

namespace Otus\Food\Domain\Kitchen\Decorator;

final class Ketchup extends Cooking
{
    /**
     * @return string[]
     */
    protected function action(): array
    {
        return ['ketchup'];
    }
}
