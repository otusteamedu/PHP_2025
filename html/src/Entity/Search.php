<?php

declare(strict_types=1);

namespace Otus\Cache\Entity;

readonly class Search
{
    /**
     * @param Conditions $conditions
     */
    public function __construct(public Conditions $conditions)
    {
    }
}
