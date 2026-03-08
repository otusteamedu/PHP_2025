<?php

namespace Igor\Test\LeetCode;

/**
 * Задача: Sort Array by Increasing Frequency
 * 
 * Условие: Дан массив целых чисел nums, отсортируйте массив в порядке возрастания частоты.
 * Если два значения имеют одинаковую частоту, отсортируйте их в порядке убывания.
 * 
 * @link https://leetcode.com/problems/sort-array-by-increasing-frequency/
 */
class SortArrayByFrequency
{
    /**
     * Сортирует массив по частоте (по возрастанию)
     * При одинаковой частоте - по убыванию значения
     * 
     * Алгоритм:
     * 1. Подсчитываем частоту каждого элемента
     * 2. Сортируем массив с кастомным компаратором:
     *    - Сначала по частоте (по возрастанию)
     *    - При равной частоте - по значению (по убыванию)
     * 
     * Временная сложность: O(n log n), где n = |nums|
     * - Подсчет частоты: O(n)
     * - Сортировка: O(n log n)
     * 
     * Пространственная сложность: O(n)
     * - Массив частот: O(n) в худшем случае (все элементы уникальны)
     * 
     * @param int[] $nums
     * @return int[]
     */
    public function frequencySort(array $nums): array
    {
        // Граничный случай: пустой массив
        if (empty($nums)) {
            return [];
        }

        // Граничный случай: один элемент
        if (count($nums) === 1) {
            return $nums;
        }

        // Подсчитываем частоту каждого элемента
        $frequency = [];
        foreach ($nums as $num) {
            $frequency[$num] = ($frequency[$num] ?? 0) + 1;
        }

        // Сортируем массив с кастомным компаратором
        usort($nums, function ($a, $b) use ($frequency) {
            $freqA = $frequency[$a];
            $freqB = $frequency[$b];

            // Если частоты разные, сортируем по частоте (по возрастанию)
            if ($freqA !== $freqB) {
                return $freqA <=> $freqB;
            }

            // Если частоты одинаковые, сортируем по значению (по убыванию)
            return $b <=> $a;
        });

        return $nums;
    }

    /**
     * Альтернативное решение с явным созданием массива пар [значение, частота]
     * 
     * Временная сложность: O(n log n)
     * Пространственная сложность: O(n)
     */
    public function frequencySortAlternative(array $nums): array
    {
        if (empty($nums)) {
            return [];
        }

        // Подсчет частоты
        $frequency = [];
        foreach ($nums as $num) {
            $frequency[$num] = ($frequency[$num] ?? 0) + 1;
        }

        // Создаем массив пар [значение, частота]
        $pairs = [];
        foreach ($nums as $num) {
            $pairs[] = ['value' => $num, 'freq' => $frequency[$num]];
        }

        // Сортируем
        usort($pairs, function ($a, $b) {
            if ($a['freq'] !== $b['freq']) {
                return $a['freq'] <=> $b['freq'];
            }
            return $b['value'] <=> $a['value'];
        });

        // Извлекаем значения
        return array_column($pairs, 'value');
    }
}
