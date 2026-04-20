<?php

/**
 * @see: https://leetcode.com/problems/two-sum/description/
 *
 * @note:
 * Временная сложность:
 * Один проход по массиву $nums - O(n), операции с хэш-таблицей - O(1).
 * Суммарно: O(n).
 *
 * Пространственная сложность:
 * Хэш-таблица может хранить до n элементов - O(n), дополнительные переменные - O(1).
 * Суммарно: O(n).
 */
class TwoSum
{
    /**
     * @param Integer[] $nums
     * @param Integer $target
     *
     * @return Integer[]
     */
    public function twoSum(array $nums, int $target): array
    {
        $hash = [];
        foreach ($nums as $k => $num) {
            $difference = $target - $num;
            if (isset($hash[$difference])) {
                return [$hash[$difference], $k];
            }
            $hash[$num] = $k;
        }

        return [];
    }
}
