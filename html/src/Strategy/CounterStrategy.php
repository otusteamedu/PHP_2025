<?php

declare(strict_types=1);

namespace Otus\Strategy;

readonly class CounterStrategy implements StrategyInterface
{
    /**
     * @param string $string
     *
     * @return bool
     */
    public function validate(string $string): bool
    {
        $counter = 0;

        $length = mb_strlen($string);

        for ($i = 0; $i < $length; $i++) {
            $char = $string[$i];

            if ($char === '(') {
                ++$counter;
            }

            if ($char === ')') {
                --$counter;
            }

            if ($counter < 0) {
                return false;
            }
        }

        return $counter === 0;
    }
}
