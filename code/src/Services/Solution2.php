<?php

declare(strict_types=1);

namespace Ak\Hw\Services;

class Solution2
{
    /**
     * @param int $numerator
     * @param int $denominator
     * @return string
     *
     * Сложность алгоритма в худшем случае будет O(n)
     * где n максимальное количество итераций  в цикле while,
     * которая зависит от дробной части.
     */
    public function fractionToDecimal(int $numerator, int $denominator): string
    {
        if ($denominator === 0) {
            return "Division by zero";
        }

        if ($numerator === 0) {
            return "0";
        }

        $result = "";
        if ( ($numerator < 0) xor ($denominator < 0) ) {
            $result .= "-";
        }

        $num = abs($numerator);
        $den = abs($denominator);

        $result .= floor($num / $den);
        $remainder = $num % $den;
        if ($remainder === 0) {
            return $result;
        }

        $result .= ".";

        $fractionalPart = "";
        $remaindersMap = [];

        // Цикл для вычисления дробной части.
        // Он продолжается до тех пор, пока остаток не станет равен 0 (т.е. деление завершится)
        // или пока не будет найден повторяющийся остаток (что указывает на бесконечную периодическую дробь).

        while ($remainder !== 0) {
            // ищем повторяющийся остаток
            if ( isset($remaindersMap[$remainder]) ) {
                $pos = $remaindersMap[$remainder];
                $result .= substr($fractionalPart, 0, $pos) . "(" . substr($fractionalPart, $pos) . ")";

                return $result;
            }

            $remaindersMap[$remainder] = strlen($fractionalPart);
            $remainder *= 10;
            $fractionalPart .= floor($remainder / $den);
            $remainder %= $den;
        }

        $result .= $fractionalPart;
        return $result;
    }
}