<?php

declare(strict_types=1);

/**
 *  @param int $numerator
 *  @param int $denominator
 *  @return string
 *
 * Временная сложность: O(k), где k - количество цифр в периоде
 * Память: O(k) - для хранения остатков
 */
function fractionToDecimal(int $numerator, int $denominator): string
{
    if ($numerator === 0) {
        return "0";
    }
        
    if ($denominator === 0) {
        return "";
    }

    $sign = ($numerator < 0) !== ($denominator < 0) ? "-" : "";

    $numerator = (int)abs($numerator);
    $denominator = (int)abs($denominator);

    $integerPart = (int)($numerator / $denominator);
    $remainder = $numerator % $denominator;

    if ($remainder === 0) {
        return $sign . $integerPart;
    }

    $decimalPart = "";
    $remainders = [];
    $remainders[$remainder] = 0;
        
    $position = 0;
    while ($remainder !== 0) {
        $remainder *= 10;
        $digit = (int)($remainder / $denominator);
        $remainder %= $denominator;
            
        $decimalPart .= $digit;

        if (isset($remainders[$remainder])) {
            $repeatStart = $remainders[$remainder];
            $decimalPart = substr($decimalPart, 0, $repeatStart) .
                "(" . substr($decimalPart, $repeatStart) . ")";
            break;
        }
            
        $position++;
        $remainders[$remainder] = $position;
    }
        
    return $sign . $integerPart . "." . $decimalPart;
}
