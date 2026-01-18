<?php

//Алгоритмическая сложность O(n+m)

declare(strict_types=1);

class Solution {

    /**
     * @param Integer[] $nums1
     * @param Integer[] $nums2
     * @return Integer[]
     */
    function intersection($nums1, $nums2) {

        $counterNums1 = [];
        $counterNums2 = [];

        foreach ($nums1 as $num) {
            $counterNums1[$num]++;
        }

        foreach ($nums2 as $num) {
            $counterNums2[$num]++;
        }

        foreach ($counterNums1 as $key => $value) {
            if (isset($counterNums2[$key])) {
                $response[] = $key;
            }
        }

        return $response ?? [];
    }
}
