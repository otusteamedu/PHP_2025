<?php

class Solution {

    /**
     * @param Integer $numerator
     * @param Integer $denominator
     * @return String
     */
    function fractionToDecimal(int $numerator, int $denominator): string {
        if ($numerator === 0) {
            return 0;
        }

        $result = ($numerator * $denominator < 0) ? '-' : '';

        $numerator = abs($numerator);
        $denominator = abs($denominator);

        $intPart = (int) ($numerator / $denominator);
        $result .= $intPart;
        $remainder = $numerator % $denominator;

        if ($remainder === 0) {
            return $result;
        }

        $hash = [];
        $result .= '.';
        $currentPosition = 0;
        $decimalPart = '';

        while ($remainder != 0) {
            if (isset($hash[$remainder])) {
                $position = $hash[$remainder];

                return $result . substr($decimalPart, 0, $position) . "(" . substr($decimalPart, $position) . ")";
            }

            $hash[$remainder] = $currentPosition++;

            $remainder = $remainder * 10;
            $decimalPart .= (int) ($remainder / $denominator);
            $remainder = $remainder % $denominator;
        }

        return $result . $decimalPart;
    }
} //сложность o(n), тк один раз проходимся по числу