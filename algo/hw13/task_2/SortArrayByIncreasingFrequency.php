<?php

/**
 * @see: https://leetcode.com/problems/sort-array-by-increasing-frequency/description/
 *
 * @note: Для PHP >= 8.0, где гарантируется стабильность сортировки
 * @see: https://wiki.php.net/rfc/stable_sorting
 *
 * @note:
 * Временная сложность:
 * array_count_values - полный проход по массиву $nums - O(n).
 * usort - обычно используется алгоритм quick sort, в среднем - O(n log n).
 * Суммарно: O(n log n).
 *
 * Пространственная сложность:
 * array_count_values - создёт массив частот - O(n).
 * usort - обычно используется алгоритм quick sort - O(log n).
 * Суммарно: O(n).
 */
class SortArrayByIncreasingFrequency
{
    /**
     * @param Integer[] $nums
     *
     * @return Integer[]
     */
    public function frequencySort(array $nums): array
    {
        $frequencies = array_count_values($nums);

        usort($nums, function(int $a, int $b) use ($frequencies) {
            // Если частоты отличаются, сравниваем по ним (по возрастанию).
            if ($frequencies[$a] !== $frequencies[$b]) {
                return $frequencies[$a] <=> $frequencies[$b];
            }
            // Если частоты равны, сравниваем по значению (по убыванию).
            return $b <=> $a;
        });

        return $nums;
    }
}
