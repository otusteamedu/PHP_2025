<?php

/**
 * @see: https://leetcode.com/problems/largest-positive-integer-that-exists-with-its-negative/description/
 *
 * @note:
 * Временная сложность:
 * 2 проходки по массиву $nums - O(n), операци сравнения и isset - O(1).
 * Суммарно: O(n).
 *
 * Пространственная сложность:
 * Массив $hash хранит положительные числа, соответствующие отрицательным, в худшем случае все - O(n).
 * Дополнительные переменные - O(1).
 * Суммарно: O(n).
 */
class LargestPositiveIntegerThatExistsWithItsNegative
{
    /**
     * @param Integer[] $nums
     *
     * @return Integer
     */
    public function findMaxK(array $nums): int
    {
        $hash = [];
        foreach ($nums as $num) {
            if ($num < 0) {
                // Сохраняем в массиве отрицательные числа как положительные
                $hash[-$num] = true; // Используем булевое значение для экономии памяти
            }
        }

        $max = -1;
        foreach ($nums as $num) {
            // Проверяем существование числа из исходного массива в хэше
            if (isset($hash[$num]) && $max < $num) {
                $max = $num;
            }
        }

        return $max;
    }
}
