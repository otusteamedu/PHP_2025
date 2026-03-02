<?php

declare(strict_types=1);

namespace Three;

/**
 * O(n)
 */
class Solution
{
    /**
     * @param int $numerator
     * @param int $denominator
     *
     * @return string
     */
    public function handle(int $numerator, int $denominator): string
    {
        if ($numerator === 0) {
            return '0';
        }

        $result = '';

        if (($numerator < 0) xor ($denominator < 0)) {
            $result .= '-';
        }

        $numerator = abs($numerator);
        $denominator = abs($denominator);

        $result .= intdiv($numerator, $denominator);
        $remainder = $numerator % $denominator;

        if ($remainder === 0) {
            return $result;
        }

        $result .= '.';

        $map = [];

        while ($remainder !== 0) {

            if (isset($map[$remainder])) {
                $pos = $map[$remainder];
                return substr($result, 0, $pos)
                    . '('
                    . substr($result, $pos)
                    . ')';
            }

            $map[$remainder] = strlen($result);

            $remainder *= 10;
            $result .= intdiv($remainder, $denominator);
            $remainder %= $denominator;
        }

        return $result;
    }
}

echo new Solution()->handle(4, 333), PHP_EOL;
