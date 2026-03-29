<?php
function fractionToDecimal($numerator, $denominator)
{
    if ($numerator == 0) {
        return '0';
    }

    $sign = (($numerator < 0 && $denominator > 0) || ($numerator > 0 && $denominator < 0)) ? '-' : '';

    $num = abs($numerator);
    $den = abs($denominator);

    $integerPart = intdiv($num, $den);
    $remainder = $num % $den;

    if ($remainder == 0) {
        return $sign . $integerPart;
    }

    $numeratorNew = $remainder;
    $position = 0;
    $remainders = [];
    $digits = '';

    while (true) {
        if (isset($remainders[$numeratorNew])) {
            $startPosition = $remainders[$numeratorNew];
            $beforePeriod = substr($digits, 0, $startPosition);
            $periodPart = substr($digits, $startPosition);

            return $sign . $integerPart . '.' . $beforePeriod . '(' . $periodPart . ')';
        }

        $remainders[$numeratorNew] = $position;

        $current = $numeratorNew * 10;
        $digit = intdiv($current, $den);
        $remainder = $current % $den;

        $digits .= $digit;

        if ($remainder == 0) {
            return $sign . $integerPart . '.' . $digits;
        }

        $numeratorNew = $remainder;
        $position++;

        if ($position > 10000) {
            return $sign . $integerPart . '.' . $digits;
        }
    }
}

//O(k) где k — длина периода