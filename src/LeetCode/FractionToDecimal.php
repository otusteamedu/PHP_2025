<?php

namespace LeetCode;

class FractionToDecimal
{
    /**
     * @param int $numerator
     * @param int $denominator
     * @return string
     */
    public function fractionToDecimal($numerator, $denominator)
    {
        if ($numerator === 0) {
            return "0";
        }

        $res = "";

        if (($numerator < 0) ^ ($denominator < 0)) {
            $res .= "-";
        }

        $num = abs($numerator);
        $den = abs($denominator);

        $res .= (string)intdiv($num, $den);
        $remainder = $num % $den;

        if ($remainder === 0) {
            return $res;
        }

        $res .= ".";
        $map = [];

        while ($remainder !== 0) {
            if (isset($map[$remainder])) {
                $res = substr_replace($res, "(", $map[$remainder], 0);
                $res .= ")";
                break;
            }

            $map[$remainder] = strlen($res);
            $remainder *= 10;
            $res .= (string)intdiv($remainder, $den);
            $remainder %= $den;
        }

        return $res;
    }
}
