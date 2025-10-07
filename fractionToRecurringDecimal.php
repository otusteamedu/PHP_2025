<?php

declare(strict_types=1);

class Solution166
{
    /**
     * Преобразует дробь в десятичное представление с выделением периодической части
     * Примеры:
     *  - 1/2  => "0.5"
     *  - 2/1  => "2"
     *  - 2/3  => "0.(6)"
     *  - -50/8 => "-6.25"
     */
    public static function fractionToDecimal(int $numerator, int $denominator): string
    {
        if ($denominator === 0) {
            throw new InvalidArgumentException('Denominator must not be zero');
        }

        if ($numerator === 0) {
            return '0';
        }

        $negative = ($numerator < 0) xor ($denominator < 0);

        $n = abs($numerator);
        $d = abs($denominator);

        $integerPart = intdiv($n, $d);
        $remainder = $n % $d;

        $result = ($negative ? '-' : '') . (string)$integerPart;

        if ($remainder === 0) {
            return $result;
        }

        $result .= '.';

        $seen = [];
        $fractionChars = [];

        while ($remainder !== 0) {
            if (isset($seen[$remainder])) {
                $repeatIndex = $seen[$remainder];
                $nonRepeat = implode('', array_slice($fractionChars, 0, $repeatIndex));
                $repeat = implode('', array_slice($fractionChars, $repeatIndex));
                $result .= $nonRepeat . '(' . $repeat . ')';
                return $result;
            }

            $seen[$remainder] = count($fractionChars);

            $remainder *= 10;
            $digit = intdiv($remainder, $d);
            $fractionChars[] = (string)$digit;
            $remainder %= $d;
        }

        $result .= implode('', $fractionChars);
        return $result;
    }
}
