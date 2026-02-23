<?php
class Solution
{
    /**
     * @param int $numerator
     * @param int $denominator
     * @return string
     */
    function fractionToDecimal($numerator, $denominator)
    {
        if ($numerator === 0) {
            return '0';
        }

        $result = '';

        if (($numerator < 0 && $denominator > 0) || ($numerator > 0 && $denominator < 0)) {
            $result .= '-';
        }

        $numerator = abs($numerator);
        $denominator = abs($denominator);

        $integerPart = intdiv($numerator, $denominator);
        $remainder = $numerator % $denominator;

        $result .= strval($integerPart);

        if ($remainder === 0) {
            return $result;
        }

        $result .= ".";

        $map = [];

        while ($remainder !== 0) {
            if (array_key_exists($remainder, $map)) {
                var_dump($remainder);
                return substr_replace($result, "(", $map[$remainder], 0) . ")";
            }

            $map[$remainder] = strlen($result);

            $remainder *= 10;
            $result .= strval(intdiv($remainder, $denominator));

            $remainder = $remainder % $denominator;
        }

        return $result;
    }
}

// Для теста
function runTest(int $numerator, int $denominator, ?string $expected): void
{
    $solution = new Solution();
    $result = $solution->fractionToDecimal($numerator, $denominator);

    $status = $result === $expected ? 'PASSED' : 'FAILED';
    echo $status . PHP_EOL;
    echo '  Input:    numerator = ' . json_encode($numerator) . ', denominator = ' . json_encode($denominator) . PHP_EOL;
    echo '  Expected: ' . json_encode($expected) . PHP_EOL;
    echo '  Got:      ' . json_encode($result) . PHP_EOL . PHP_EOL;
}

echo '=== fractionToDecimal ===' . PHP_EOL . PHP_EOL;

// Примеры из задачи
// Пример 1
$numerator = 1;
$denominator = 2;
$expected = "0.5";
runTest($numerator, $denominator, $expected);

// Пример 2: 
$numerator = 2;
$denominator = 1;
$expected = "2";
runTest($numerator, $denominator, $expected);

// Пример 3: 
$numerator = 4;
$denominator = 333;
$expected = "0.(012)";
runTest($numerator, $denominator, $expected);
