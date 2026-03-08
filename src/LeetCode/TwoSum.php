<?php

namespace Igor\Test\LeetCode;

/**
 * Задача: Two Sum
 * 
 * Условие: Дан массив целых чисел nums и целое число target.
 * Верните индексы двух чисел таких, что их сумма равна target.
 * Можно предположить, что решение существует и оно единственное.
 * Нельзя использовать один и тот же элемент дважды.
 * 
 * @link https://leetcode.com/problems/two-sum/
 */
class TwoSum
{
    /**
     * Находит два числа, сумма которых равна target
     * 
     * Алгоритм (One-pass Hash Table):
     * 1. Проходим по массиву один раз
     * 2. Для каждого элемента вычисляем complement = target - nums[i]
     * 3. Проверяем, есть ли complement в хеш-таблице
     * 4. Если есть - возвращаем индексы, если нет - добавляем текущий элемент в таблицу
     * 
     * Временная сложность: O(n), где n = |nums|
     * - Один проход по массиву: O(n)
     * - Операции с хеш-таблицей: O(1) в среднем
     * 
     * Пространственная сложность: O(n)
     * - Хеш-таблица для хранения элементов и их индексов: O(n) в худшем случае
     * 
     * @param int[] $nums
     * @param int $target
     * @return int[] Массив из двух индексов [index1, index2]
     */
    public function twoSum(array $nums, int $target): array
    {
        // Граничный случай: массив должен содержать минимум 2 элемента
        if (count($nums) < 2) {
            throw new \InvalidArgumentException("Array must contain at least 2 elements");
        }

        // Хеш-таблица: значение => индекс
        $map = [];

        // Проходим по массиву один раз
        foreach ($nums as $index => $num) {
            // Вычисляем недостающее число (complement)
            $complement = $target - $num;

            // Проверяем, есть ли complement в таблице
            if (isset($map[$complement])) {
                // Нашли пару! Возвращаем индексы
                return [$map[$complement], $index];
            }

            // Добавляем текущий элемент в таблицу (после проверки, чтобы избежать использования одного элемента дважды)
            $map[$num] = $index;
        }

        // По условию задачи решение всегда существует, но на всякий случай
        throw new \RuntimeException("No solution found");
    }

    /**
     * Альтернативное решение: Brute Force
     * 
     * Временная сложность: O(n²)
     * Пространственная сложность: O(1)
     * 
     * Менее эффективно, но проще для понимания
     */
    public function twoSumBruteForce(array $nums, int $target): array
    {
        if (count($nums) < 2) {
            throw new \InvalidArgumentException("Array must contain at least 2 elements");
        }

        $n = count($nums);

        // Проверяем все пары
        for ($i = 0; $i < $n - 1; $i++) {
            for ($j = $i + 1; $j < $n; $j++) {
                if ($nums[$i] + $nums[$j] === $target) {
                    return [$i, $j];
                }
            }
        }

        throw new \RuntimeException("No solution found");
    }
}
