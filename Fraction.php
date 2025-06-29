<?php

declare(strict_types=1);

class Solution
{
    /**
     * @param int $numerator
     * @param int $denominator
     * @return string
     */
    function fractionToDecimal(int $numerator, int $denominator): string
    {
        if ($numerator === 0) {
            return "0";
        }

        $result = "";
        // Определяем знак
        if (($numerator < 0) xor ($denominator < 0)) {
            $result .= "-";
        }

        $numerator = abs($numerator);
        $denominator = abs($denominator);

        // Целая часть
        $integerPart = intval($numerator / $denominator);
        $result .= $integerPart;

        $remainder = $numerator % $denominator;
        if ($remainder === 0) {
            return $result;
        }

        $result .= ".";
        $remainderMap = [];

        return $this->fractionToDecimalRecursive($remainder, $denominator, $result, $remainderMap);
    }

    private function fractionToDecimalRecursive($remainder, $denominator, &$result, &$remainderMap)
    {
        if ($remainder == 0) {
            return $result;
        }

        if (isset($remainderMap[$remainder])) {
            $result = substr_replace($result, "(", $remainderMap[$remainder], 0);
            $result .= ")";
            return $result;
        }

        $remainderMap[$remainder] = strlen($result);
        $remainder *= 10;
        $digit = intval($remainder / $denominator);
        $result .= $digit;
        $remainder = $remainder % $denominator;

        return $this->fractionToDecimalRecursive($remainder, $denominator, $result, $remainderMap);
    }

}
