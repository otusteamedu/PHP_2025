<?php

/**
 * @see: https://leetcode.com/problems/how-many-numbers-are-smaller-than-the-current-number/description/
 *
 * @note:
 * Временная сложность:
 * Сортировка O(n log n), 2 проходки по массивам - O(2n).
 * Суммарно: O(n log n).
 *
 * Пространственная сложность:
 * Копия исходного массива - O(n).
 * Массив для хранения позиций уникальных чисел: в худшем случае - O(n), массив результата - O(n).
 * Суммарно: O(n).
 */
class HowManyNumbersAreSmallerThanTheCurrentNumber
{
    /**
     * @param Integer[] $nums
     *
     * @return Integer[]
     */
    public function smallerNumbersThanCurrent(array $nums): array
    {
        $sorted = $nums;
        sort($sorted);

        // Для каждого уникального числа запоминаем его первую позицию в отсортированном массиве $nums.
        // Каждая такая позизия - это и есть количество элемнтов меньше самого числа.
        $positions = [];
        for ($i = 0; $i < count($sorted); $i++) {
            if (!isset($positions[$sorted[$i]])) {
                $positions[$sorted[$i]] = $i;
            }
        }

        $result = [];
        foreach ($nums as $num) {
            $result[] = $positions[$num];
        }

        return $result;
    }
}
