<?php

declare(strict_types=1);

namespace Otus\Cache\Entity;

readonly class Conditions
{
    /**
     * @param array $conditions
     */
    public function __construct(public array $conditions)
    {
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->conditions;
    }
}
