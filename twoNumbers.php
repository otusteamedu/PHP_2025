<?php
class Solution {

    /**
     * https://leetcode.com/problems/two-sum/description/
     *
     * @param $nums
     * @param $target
     * @return array
     */
    function twoSum($nums, $target) {
        $map = [];
        for($i = 0; $i < count($nums); $i++) {
            $diff = $target - $nums[$i];

            if (isset($map[$diff])) {
                return [$i, $map[$diff]];
            }
            $map[$nums[$i]] = $i;
        }
        return [];
    }
}

//Сложность O(n).
var_dump((new Solution)->twoSum([3,2,4], 6));
