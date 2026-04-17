<?php

/**
 * @see: https://leetcode.com/problems/intersection-of-two-arrays/description/
 *
 * @note:
 * Временная сложность:
 * Цикл по $nums2 - O(n), цикл по $nums1 - O(m), операции вставки и проверки isset - O(1).
 * Суммарно: O(n + m).
 *
 * Пространственная сложность:
 * В худшем случае все элементы nums2 уникальны и совпадают с nums1 - O(2n).
 * Суммарно: O(n).
 */
class IntersectionOfTwoArrays
{
    /**
     * @param Integer[] $nums1
     * @param Integer[] $nums2
     *
     * @return Integer[]
     */
    public function intersection(array $nums1, array $nums2): array
    {
        // Сделаем множество из второго массива.
        $set2 = [];
        foreach ($nums2 as $num) {
            if (!isset($set2[$num])) {
                $set2[$num] = true; // Используем булевое значение для экономии памяти.
            }
        }

        $result = [];
        foreach ($nums1 as $num) {
            if (isset($set2[$num]) && !isset($result[$num])) {
                $result[$num] = $num;
            }
        }

        return $result;
    }
}
