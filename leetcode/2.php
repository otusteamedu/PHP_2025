<?php

/**
 *  Сложность O(denominator)
 */
class Solution {

    /**
     * @param Integer $numerator
     * @param Integer $denominator
     * @return String
     */
    function fractionToDecimal($numerator, $denominator) {
        $result = '';

        if (($numerator < 0 && $denominator > 0) || ($numerator > 0 && $denominator < 0)) {
            $result .= '-';
        }

        $numeratorAbs = abs($numerator);
        $denominatorAbs = abs($denominator);

        $integerPart = intdiv($numeratorAbs, $denominatorAbs);
        $result .= $integerPart;

        $remainder = $numeratorAbs % $denominatorAbs;
        if ($remainder === 0) {
            return $result;
        }

        $result .= '.';

        $remainderPosition = [];
        while ($remainder !== 0) {
            if (isset($remainderPosition[$remainder])) {
                $index = $remainderPosition[$remainder];
                $prefix = substr($result, 0, $index);
                $repeat = substr($result, $index);
                $result = "{$prefix}({$repeat})";

                return $result;
            }

            $remainderPosition[$remainder] = strlen($result);

            $remainder *= 10;
            $digit = intdiv($remainder, $denominatorAbs);
            $result .= $digit;
            $remainder %= $denominatorAbs;
        }

        return $result;
    }
}
