<?php

declare(strict_types=1);

namespace Otus\Food\Domain\Kitchen\Decorator;

final class Cucumber extends Cooking
{
    /**
     * @return string[]
     */
    protected function action(): array
    {
        return ['cucumber'];
    }
}
