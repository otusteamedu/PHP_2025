<?php

//Сложность решения O(n logn), т.к. функция sort имеет сложность O(n logn)

class Solution {

    /**
     * @param Integer[] $nums
     * @return Integer[]
     */
    function smallerNumbersThanCurrent($nums) {
        $result = [];
        $hash = [];
        $nums2 = $nums;

        sort($nums);

        $countNums = count($nums);
        $hash[$nums[0]] = 0;
        for($i = 1; $i < $countNums; $i++) {
            if ($nums[$i] > $nums[$i - 1]) {
                $hash[$nums[$i]] = $i;
            }
        }

        for($i = 0; $i < $countNums; $i++) {
            $result[$i] = $hash[$nums2[$i]];
        }

        return $result;
    }
}