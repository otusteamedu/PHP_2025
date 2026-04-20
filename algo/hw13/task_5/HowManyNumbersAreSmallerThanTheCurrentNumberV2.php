<?php

/**
 * @see: https://leetcode.com/problems/how-many-numbers-are-smaller-than-the-current-number/description/
 *
 * @note:
 * Временная сложность:
 * Подготовка массива для частот через array_fill - O(k).
 * Заполнение частот в цикле - O(n).
 * Подготовка массива префиксных сумм через array_fill - O(k).
 * Расчёт префексных сумм в цикле - O(k).
 * Формирование результата - O(n).
 * Суммарно: O(n + k).
 *
 * Пространственная сложность:
 * Массив частот - O(k), массив префиксных сумм - O(k).
 * Результирующий массив - O(n).
 * Суммарно: O(k).
 */
class HowManyNumbersAreSmallerThanTheCurrentNumberV2
{
    public const int MAX_VALUE = 100;

    /**
     * @param Integer[] $nums
     *
     * @return Integer[]
     */
    public function smallerNumbersThanCurrent(array $nums): array
    {
        $count = self::MAX_VALUE + 1;

        // Массив частот для чисел от 0 до 100
        $frequencies = array_fill(0, $count, 0);

        // Заполняем частоты
        foreach ($nums as $num) {
            $frequencies[$num]++;
        }

        // Префиксные суммы: prefix[i] = количество чисел < i
        $prefix = array_fill(0, $count, 0);
        for ($i = 1; $i <= self::MAX_VALUE; $i++) {
            $prefix[$i] = $prefix[$i - 1] + $frequencies[$i - 1];
        }

        $result = [];
        foreach ($nums as $num) {
            $result[] = $prefix[$num];
        }

        return $result;
    }
}

$result = new HowManyNumbersAreSmallerThanTheCurrentNumberV2()->smallerNumbersThanCurrent([0,8,1,100,3]);
var_dump($result);
