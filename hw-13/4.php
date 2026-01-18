<?php

//Алгоритмическая сложность O(n log n) из за krsort

declare(strict_types=1);

class Solution {

    /**
     * @param Integer[] $nums
     * @return Integer
     */
    function findMaxK($nums) {
        $counterNums = [];

        foreach ($nums as $num) {
            $counterNums[$num]++;
        }

        krsort($counterNums);

        foreach ($counterNums as $key => $value) {
            if (array_key_exists(-$key, $counterNums)) {
                return $key;
            }
        }

        return -1;
    }
}
