<?php

//https://leetcode.com/problems/fraction-to-recurring-decimal/

class Solution
{

    /**
     * @param Integer $numerator
     * @param Integer $denominator
     * @return String
     */
    function fractionToDecimal($numerator, $denominator)
    {
        $number = $numerator / $denominator;
        $precision = 3;
        $pos = strrpos($number, '.');
        if ($pos !== false) {
            $number = substr($number, 0, $pos + 1 + $precision);
        }

        return $number;
    }
}

$result = new Solution()->fractionToDecimal(2, 1);
var_dump($result);
