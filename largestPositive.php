<?php
class Solution {

    /**
     * https://leetcode.com/problems/largest-positive-integer-that-exists-with-its-negative/description/
     *
     * @param Integer[] $nums
     * @return Integer
     */
    function findMaxK($nums) {
        $tmp = [];
        $maxK = -1;

        foreach ($nums as $num) {
            if (isset($tmp[-$num])) {
                $maxK = max($maxK, abs($num));
            }
            $tmp[$num] = true;
        }

        return $maxK;
    }
}

//Сложность: O(n).
var_dump((new Solution)->findMaxK([-1,2, -2,3]));