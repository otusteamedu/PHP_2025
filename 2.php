<?php

class Solution {

    /**
     * @param Integer[] $nums
     * @return Integer[]
     */
    function frequencySort(array $nums): array {
        $hash = [];

        foreach ($nums as $num) {
            $hash[$num]++;
        }

        usort(
            $nums,
            function ($val1, $val2) use ($hash) {
                if ($hash[$val1] === $hash[$val2]) {
                    return $val2 <=> $val1;
                }

                return $hash[$val1] <=> $hash[$val2];
            }
        );

        return $nums;
    }
} //сложность o(n log n) - сложность usort, foreach работает быстрее, поэтому пропускаем