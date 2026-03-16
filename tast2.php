<?php

declare(strict_types=1);

class Solution {

    public function fractionToDecimal(int $numerator, int $denominator): string
    {
        $result = '';
        $isMinusSign = ($numerator < 0 ^ $denominator < 0) && $numerator !== 0;

        if($isMinusSign) {
            $result .= '-';
        }

        $numerator = abs($numerator);
        $denominator = abs($denominator);

        $result .=  intdiv($numerator, $denominator);

        $remains = $numerator % $denominator;

        if ($remains === 0) {
            return $result;
        }
        $result .= '.';
        $fractionalPart = '';

        $arRemains = [];
        while ($remains !== 0) {
            if (isset($arRemains[$remains])) {
                $pos = $arRemains[$remains];
                return
                    $result .
                    substr($fractionalPart, 0, $pos) .
                    '(' .
                    substr($fractionalPart, $pos) .
                    ')';
            }

            $arRemains[$remains] = strlen($fractionalPart);

            $remains *= 10;
            $int = intdiv($remains, $denominator);
            $remains = $remains % $denominator;

            $fractionalPart .= $int;
        }

        return $result . $fractionalPart;
    }
}

// $solution = new Solution();
//
// $res = $solution->fractionToDecimal(1, -1);
// $res = $solution->fractionToDecimal(1, 6);
//
// echo $res;