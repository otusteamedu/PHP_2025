<?php

class Solution {

    /**
     * https://leetcode.com/problems/two-sum/description/
     * @param Integer[] $nums
     * @param Integer $target
     * @return Integer[]
     */
    function twoSum($nums, $target) {
        $hash = [];
        for ($i = 0; $i < count($nums); $i++) {
            if (!isset($hash[$target - $nums[$i]])) {
                $hash[$target - $nums[$i]] = $i;
            }

            if (isset($hash[$nums[$i]]) && $i !== $hash[$nums[$i]]) {
                return [$i, $hash[$nums[$i]]];
            }
        }

        return [];
    }
}