<?php

declare(strict_types=1);

namespace Ak\Hw\Services;

class Solution2
{
    /**
     * @param int $numerator
     * @param int $denominator
     * @return string
     */
    public function fractionToDecimal(int $numerator, int $denominator): string
    {
        if ($denominator === 0) {
            return "Division by zero";
        }

        if ($numerator === 0) {
            return "0";
        }

        $result = "";
        if ( ($numerator < 0) xor ($denominator < 0) ) {
            $result .= "-";
        }

        $num = abs($numerator);
        $den = abs($denominator);

        $result .= floor($num / $den);
        $remainder = $num % $den;

        if ($remainder === 0) {
            return $result;
        }

        $result .= ".";

        $fractionalPart = "";
        $remaindersMap = [];

        while ($remainder !== 0 && !isset($remaindersMap[$remainder])) {
            $remaindersMap[$remainder] = strlen($fractionalPart);

            $remainder *= 10;
            $fractionalPart .= floor($remainder / $den);
            $remainder %= $den;
        }

        if ($remainder === 0) {
            $result .= $fractionalPart;
        } else {
            $pos = $remaindersMap[$remainder];
            $result .= substr($fractionalPart, 0, $pos) . "(" . substr($fractionalPart, $pos) . ")";
        }

        return $result;
    }
}