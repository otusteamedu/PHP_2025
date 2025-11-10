<?php


class Solution {

    /**
     * https://leetcode.com/problems/largest-positive-integer-that-exists-with-its-negative/description/
     * @param Integer[] $nums
     * @return Integer
     */
    function findMaxK($nums) {
        $hash = [];
        foreach ($nums as $number) {
            if ($number < 0) {
                $hash[abs($number)] = $number;
            }
        }
        $returnNumber = -1;
        foreach ($nums as $number) {
            if ($number < 0) {
                continue;
            }
            if (isset($hash[$number]) && $number > $returnNumber) {
                $returnNumber = $number;
            }
        }

        return $returnNumber;
    }
}