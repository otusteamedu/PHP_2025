<?php

declare(strict_types=1);

/**
 * @see: https://leetcode.com/problems/fraction-to-recurring-decimal/description
 *
 * @note:
 * Временная сложность:
 * O(d), где d - значение знаменателя; в худшем случае d-1 итераций цикла
 *
 * Пространственная сложность:
 * Для хранения $mapRemainders и $fractionalDigits максимум 2(d-1) или просто O(d)
 */
class FractionToRecurringDecimal
{
    public function fractionToDecimal(int $numerator, int $denominator): string
    {
        // Если числитель 0, то результат всегда 0; знаменатель по условию задачи !== 0, поэтому не проверяем этот кейс
        if ($numerator === 0) {
            return "0";
        }

        // Определяем знак результата: минус, если знаки операндов разные
        $isNegativeResult = ($numerator < 0) !== ($denominator < 0);

        // Дальнейшие операции только с положительными числами
        $numerator = abs($numerator);
        $denominator = abs($denominator);

        // Целая часть и остаток
        $integerPart = intdiv($numerator, $denominator);
        $remainder = $numerator % $denominator;

        // Если дробной части нет - сразу формируем результат
        if ($remainder === 0) {
            $result = (string) $integerPart;
        } else {
            // Эмуляция деления в столбик для дробной части
            $fractionalDigits = [];
            $mapRemainders = []; // остаток => его позиция в дробной части
            $reminderPos = 0;
            $hasPeriod = false;

            while ($remainder !== 0) {
                if (isset($mapRemainders[$remainder])) {
                    // Если остаток уже встречался, то нашли период
                    $hasPeriod = true;
                    $idx = $mapRemainders[$remainder];
                    $nonRepeatingPart = implode('', array_slice($fractionalDigits, 0, $idx));
                    $repeatingPart = implode('', array_slice($fractionalDigits, $idx));
                    $result = "$integerPart.$nonRepeatingPart($repeatingPart)";
                    break;
                }

                $mapRemainders[$remainder] = $reminderPos;
                $remainder *= 10;
                $digit = intdiv($remainder, $denominator);
                $fractionalDigits[] = (string) $digit;
                $remainder %= $denominator;
                $reminderPos++;
            }

            // Если цикл завершился без обнаружения периода, то дробь конечная
            if (!$hasPeriod) {
                $result = $integerPart . '.' . implode('', $fractionalDigits);
            }
        }

        return $isNegativeResult ? "-$result" : $result;
    }
}
