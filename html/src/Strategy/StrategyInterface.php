<?php

declare(strict_types=1);

namespace Otus\Strategy;

interface StrategyInterface
{
    /**
     * @param string $string
     *
     * @return bool
     */
    public function validate(string $string): bool;
}
