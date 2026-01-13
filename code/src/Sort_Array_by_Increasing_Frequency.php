<?php

//Сложность решения O(n logn), т.к. функция usort имеет сложность O(n logn)
class Solution {

    /**
     * @param Integer[] $nums
     * @return Integer[]
     */
    function frequencySort($nums) {
        $hash = [];
        $countNums = count($nums);
        for($i = 0; $i < $countNums; $i++) {
            $hash[$nums[$i]]++;
        }

        usort($nums, function($a, $b) use ($hash){
            if ($hash[$a] == $hash[$b]) {
                return ($a < $b) ? 1 : -1;
            }

            return ($hash[$a] < $hash[$b]) ? -1 : 1;
        });

        return $nums;
    }
}