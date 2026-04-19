<?php

/**
 * @see: https://leetcode.com/problems/sort-array-by-increasing-frequency/description/
 *
 * @note: Для PHP < 8.0, где не гарантируется исходный порядок для элементов с одинаковым результатом сравнения.
 * @see: https://wiki.php.net/rfc/stable_sorting
 *
 * @note:
 * Временная сложность:
 * array_count_values - полный проход по массиву $nums - O(n).
 * модификация исходного массива - полный проход по массиву $nums - O(n).
 * usort - обычно используется алгоритм quick sort, в среднем - O(n log n).
 * array_column - полный проход по массиву $nums - O(n).
 * Суммарно: O(n log n).
 *
 * Пространственная сложность:
 * array_count_values - создёт массив частот - O(n).
 * модификация исходного массива - O(2n).
 * usort - обычно используется алгоритм quick sort - O(log n).
 * array_column - создаёт результирующий массив - O(n).
 * Суммарно: O(n).
 */
class SortArrayByIncreasingFrequencyV2
{
    /**
     * @param Integer[] $nums
     *
     * @return Integer[]
     */
    public function frequencySort(array $nums): array
    {
        $frequencies = array_count_values($nums);

        // Меняем значения исходного массива, будем хранить дополнительно индексы
        foreach ($nums as $k => $num) {
            $nums[$k] = [$num, $k];
        }

        usort($nums, function(array $a, array $b) use ($frequencies) {
            // Если частоты отличаются, сравниваем по ним (по возрастанию).
            if ($frequencies[$a[0]] !== $frequencies[$b[0]]) {
                return $frequencies[$a[0]] <=> $frequencies[$b[0]];
            }

            // Если частоты равны, сравниваем по значению (по убыванию)
            // Если и значения равны, сохраняем исходный порядок (по индексу)
            return $a[0] !== $b[0] ? $b[0] <=> $a[0] : $a[1] <=> $b[1];
        });

        return array_column($nums, 0);
    }
}
