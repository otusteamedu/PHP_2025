<?php

declare(strict_types=1);

class Solution2
{
    /**
     * @param Integer $numerator
     * @param Integer $denominator
     * @return String
     */
    function fractionToDecimal(int $numerator, int $denominator): string
    {
        if ($numerator == 0) {
            return "0";
        }
        $result = "";
        // Определяем знак
        if (($numerator < 0) ^ ($denominator < 0)) {
            $result .= "-";
        }

        // Работаем с абсолютными значениями
        $numerator = abs($numerator);
        $denominator = abs($denominator);

        // Целая часть
        $result .= floor($numerator / $denominator);
        $remainder = $numerator % $denominator;

        if ($remainder == 0) {
            return $result;
        }

        $result .= ".";
        $remainderMap = array();
        $repeating = false;
        $index = strlen($result);

        while ($remainder != 0) {
            if (isset($remainderMap[$remainder])) {
                $index = $remainderMap[$remainder];
                $repeating = true;
                break;
            }

            $remainderMap[$remainder] = $index;
            $remainder *= 10;
            $result .= floor($remainder / $denominator);
            $remainder %= $denominator;
            $index++;
        }

        if ($repeating) {
            $result = substr($result, 0, $index) . "(" . substr($result, $index) . ")";
        }

        return $result;
    }
}