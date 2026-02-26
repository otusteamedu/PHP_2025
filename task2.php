<?php
// Решение задачи: Fraction to Recurring Decimal
// Сложность: O(n), где n — длина периода

function fractionToDecimal(int $numerator, int $denominator): string
{
    if ($numerator === 0) {
        return "0";
    }

    $result = (($numerator < 0) xor ($denominator < 0)) ? '-' : '';

    $num = abs($numerator);
    $den = abs($denominator);

    $integerPart = intdiv($num, $den);
    $result .= $integerPart;

    $remainder = $num % $den;

    if ($remainder === 0) {
        return $result;
    }

    $result .= '.';

    $remainderPositions = [];
    $decimalDigits = '';
    $position = 0;

    while ($remainder !== 0) {
        if (isset($remainderPositions[$remainder])) {
            $nonRepeating = substr($decimalDigits, 0, $remainderPositions[$remainder]);
            $repeating = substr($decimalDigits, $remainderPositions[$remainder]);
            return $result . $nonRepeating . '(' . $repeating . ')';
        }

        $remainderPositions[$remainder] = $position;

        $remainder *= 10;

        $digit = intdiv($remainder, $den);
        $decimalDigits .= $digit;

        $remainder = $remainder % $den;

        $position++;
    }

    return $result . $decimalDigits;
}

echo "--- Тест1:\n";
$result = fractionToDecimal(1, 2);
echo "Результат: $result\n";

echo "--- Тест2:\n";
$result = fractionToDecimal(2, 1);
echo "Результат: $result\n";

echo "--- Тест3:\n";
$result = fractionToDecimal(4, 333);
echo "Результат: $result\n";
