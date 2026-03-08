<?php

namespace Igor\Test\LeetCode;

/**
 * Задача: Largest Positive Integer That Exists With Its Negative
 * 
 * Условие: Дан массив целых чисел nums, не содержащий нулей.
 * Найдите наибольшее положительное целое число k такое, что -k также существует в массиве.
 * Верните положительное число k. Если такого числа нет, верните -1.
 * 
 * @link https://leetcode.com/problems/largest-positive-integer-that-exists-with-its-negative/
 */
class LargestPositiveInteger
{
    /**
     * Находит наибольшее положительное число, для которого есть отрицательное
     * 
     * Алгоритм:
     * 1. Создаем множество из всех чисел для O(1) поиска
     * 2. Проходим по массиву, ищем положительные числа
     * 3. Для каждого положительного числа проверяем наличие его отрицания
     * 4. Отслеживаем максимальное такое число
     * 
     * Временная сложность: O(n), где n = |nums|
     * - Создание множества: O(n)
     * - Проход по массиву: O(n)
     * - Проверка наличия в множестве: O(1) в среднем
     * 
     * Пространственная сложность: O(n)
     * - Множество для хранения всех чисел: O(n)
     * 
     * @param int[] $nums
     * @return int
     */
    public function findMaxK(array $nums): int
    {
        // Граничный случай: пустой массив
        if (empty($nums)) {
            return -1;
        }

        // Создаем множество для быстрого поиска
        $numSet = array_flip($nums);
        
        $maxK = -1;

        // Проходим по массиву
        foreach ($nums as $num) {
            // Ищем только положительные числа
            if ($num > 0) {
                // Проверяем, существует ли отрицательное число
                if (isset($numSet[-$num])) {
                    // Обновляем максимум
                    $maxK = max($maxK, $num);
                }
            }
        }

        return $maxK;
    }

    /**
     * Альтернативное решение с двумя множествами
     * 
     * Временная сложность: O(n)
     * Пространственная сложность: O(n)
     */
    public function findMaxKAlternative(array $nums): int
    {
        if (empty($nums)) {
            return -1;
        }

        $positiveSet = [];
        $negativeSet = [];

        // Разделяем числа на положительные и отрицательные
        foreach ($nums as $num) {
            if ($num > 0) {
                $positiveSet[$num] = true;
            } else {
                $negativeSet[abs($num)] = true;
            }
        }

        $maxK = -1;

        // Ищем максимальное положительное число, для которого есть отрицательное
        foreach ($positiveSet as $num => $_) {
            if (isset($negativeSet[$num])) {
                $maxK = max($maxK, $num);
            }
        }

        return $maxK;
    }
}
