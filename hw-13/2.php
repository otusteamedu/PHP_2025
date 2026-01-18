<?php

//Алгоритмическая сложность O(n log n) из за uasort

declare(strict_types=1);

class Solution {

    /**
     * @param Integer[] $nums
     * @return Integer[]
     */
    function frequencySort($nums){
        $arrCounts = [];

        foreach ($nums as $num) {
            $arrCounts[$num]++;
        }

        uasort($nums, function ($a, $b) use ($arrCounts) {
            if ($arrCounts[$a] === $arrCounts[$b]) {
                return $b <=> $a;
            }
            return $arrCounts[$a] <=> $arrCounts[$b];
        });

        return $nums;
    }
}
