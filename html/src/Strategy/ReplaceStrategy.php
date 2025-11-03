<?php

declare(strict_types=1);

namespace Otus\Strategy;

readonly class ReplaceStrategy implements StrategyInterface
{
    /**
     * @param string $string
     *
     * @return bool
     */
    public function validate(string $string): bool
    {
        $condition = true;

        do {
            $after = str_replace('()', '', $string);

            if ($after === $string) {
                $condition = false;
            } else {
                $string = $after;
            }
        } while ($condition);

        return mb_strlen($string) === 0;
    }
}
