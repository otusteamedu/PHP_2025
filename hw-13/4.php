<?php

//Алгоритмическая сложность O(n)

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

        $maxNum = null;
        foreach ($counterNums as $key => $value) {
            if (array_key_exists(-$key, $counterNums)) {
                if ($maxNum === null || $key > $maxNum) {
                    $maxNum = $key;
                }
            }
        }

        return $maxNum ?? -1;
    }
}
