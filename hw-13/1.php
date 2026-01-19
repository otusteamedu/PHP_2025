<?php

//Алгоритмическая сложность O(n)

declare(strict_types=1);

class Solution {

    /**
     * @param Integer[] $nums
     * @param Integer $target
     * @return Integer[]
     */
    function twoSum($nums, $target) {
        $counterNums = [];

        foreach ($nums as $key => $value) {
            $needValue = $target - $value;

            if (array_key_exists($needValue, $counterNums)) {
                return [$counterNums[$needValue], $key];
            }

            $counterNums[$value] = $key;
        }

        return [];
    }
}
