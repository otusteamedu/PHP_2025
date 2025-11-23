<?php
declare(strict_types=1);

class Solution
{
    function fractionToDecimal(int $numerator, int $denominator): string
    {
        if ($denominator === 0) {
            throw new \RuntimeException('You can\'t divide by 0');
        }

        if ($numerator === 0) {
            return (string)$numerator;
        }

        $fraction = '';
        if ($numerator < 0 && $denominator < 0) {
            $fraction .= '';
        } else if ($numerator < 0 || $denominator < 0) {
            $fraction .= '-';
        }

        $numerator = abs($numerator);
        $denominator = abs($denominator);

        $fraction .= floor($numerator / $denominator);
        $remainder = ($numerator % $denominator);
        if ($remainder === 0) {
            return $fraction;
        }

        $length = 10;
        $fraction .= '.';
        $remainderMap = [];
        $isRepeating = false;
        $lineLength = strlen($fraction);

        while ($remainder !== 0) {
            if (isset($remainderMap[$remainder])) {
                $lineLength = $remainderMap[$remainder];
                $isRepeating = true;
                break;
            }

            $remainderMap[$remainder] = $lineLength;
            $remainder *= $length;
            $fraction .= floor($remainder / $denominator);
            $remainder %= $denominator;
            $lineLength++;
        }

        if ($isRepeating === true) {
            $fraction = substr($fraction, 0, $lineLength) . '(' . substr($fraction, $lineLength) . ')';
        }

        return $fraction;
    }
}


$arNumbers = [
    [1, 2],
    [2, 1],
    [4, 333],
    [-1, 55],
    [0, 3],
    [12, -2],
    [-20, -3],
    [77, 0]
];


$obSolution = new Solution();
foreach ($arNumbers as $arItemNumber) {
    if (count($arItemNumber) === 2) {
        list($numerator, $denominator) = $arItemNumber;

        try {
            $fraction = $obSolution->fractionToDecimal($numerator, $denominator);
            echo 'Fraction of a number: ' . $numerator . '/' . $denominator . ' = ' . $fraction . PHP_EOL;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage() . ' (fraction of a number: ' . $numerator . '/' . $denominator . ')' . PHP_EOL;
        }
    }
}