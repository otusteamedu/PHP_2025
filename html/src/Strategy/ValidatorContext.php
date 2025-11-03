<?php

declare(strict_types=1);

namespace Otus\Strategy;

readonly class ValidatorContext
{
    /**
     * @param StrategyInterface $strategy
     */
    public function __construct(
        protected StrategyInterface $strategy
    ) {
    }

    /**
     * @param string $string
     *
     * @return bool
     */
    public function validate(string $string): bool
    {
        return $this->strategy->validate($string);
    }
}
