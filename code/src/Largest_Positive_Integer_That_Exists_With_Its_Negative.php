<?php

//Сложность решения O(n)
class Solution {

    /**
     * @param Integer[] $nums
     * @return Integer
     */
    function findMaxK($nums) {
        $result = -1;
        $hash = [];
        $countNums = count($nums);
        for($i = 0; $i < $countNums; $i++) {
            $hash[$nums[$i]] = 1;
        }

        for($i = 0; $i < $countNums; $i++) {
            if (isset($hash[$nums[$i] * -1]) && $nums[$i] > $result) {
                $result = $nums[$i];
            }
        }

        return $result;
    }
}