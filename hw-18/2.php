<?php

// Алгоритмическая сложность О(denominator)

declare(strict_types=1);

class Solution {

    /**
     * @param Integer $numerator
     * @param Integer $denominator
     * @return String
     */
    function fractionToDecimal($numerator, $denominator) {
        if ($numerator * $denominator < 0) {
            $sign = '-';
        } else {
            $sign = '';
        }

        $numerator = abs($numerator);
        $denominator = abs($denominator);

        $integerPart = intdiv($numerator, $denominator);
        $remainder = $numerator % $denominator;

        if ($remainder == 0) {
            return $sign . $integerPart;
        }

        $decimalPart = '';
        $remainders = [];

        while ($remainder != 0) {
            if (isset($remainders[$remainder])) {
                $repeatStart = $remainders[$remainder];
                return $sign . $integerPart . '.' . substr($decimalPart, 0, $repeatStart) . '(' . substr($decimalPart, $repeatStart) . ')';
            }

            $remainders[$remainder] = strlen($decimalPart);

            $remainder *= 10;
            $decimalDigit = intdiv($remainder, $denominator);
            $decimalPart .= $decimalDigit;
            $remainder %= $denominator;
        }

        return $sign . $integerPart . '.' . $decimalPart;
    }
}