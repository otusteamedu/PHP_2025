<?php

declare(strict_types=1);

namespace Otus\Food\Domain\Order;

use Stringable;

readonly class Buyer implements Stringable
{
    /**
     * @param string $name
     */
    public function __construct(public string $name)
    {
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
