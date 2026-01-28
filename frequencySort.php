<?php
class Solution {

    /**
     * https://leetcode.com/problems/sort-array-by-increasing-frequency/description/
     *
     * @param Integer[] $nums
     * @return Integer[]
     */
    function frequencySort($nums) {
        $frequencyNumbers = [];
        for ($i = 0; $i < count($nums); $i++) {
            $key = $nums[$i];
            if (isset($frequencyNumbers[$key])) {
                $frequencyNumbers[$key]++;
            } else {
                $frequencyNumbers[$key] = 1;
            }
        }
        usort($nums, function ($a, $b) use ($frequencyNumbers) {
            if ($frequencyNumbers[$a] == $frequencyNumbers[$b]) {
                return $a < $b ? 1 : -1;
            }
            return $frequencyNumbers[$a] > $frequencyNumbers[$b] ? 1 : -1;
        });
        return $nums;
    }
}
//Сложность: O(n log n)
var_dump((new Solution)->frequencySort([2,15,15,7,]));