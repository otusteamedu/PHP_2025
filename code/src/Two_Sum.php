<?php

//Сложность решения O(n)
class Solution {

    /**
     * @param Integer[] $nums
     * @param Integer $target
     * @return Integer[]
     */
    function twoSum($nums, $target) {
        $hash = [];
        $countNums = count($nums);
        for($i = 0; $i < $countNums; $i++) {
            $hash[$nums[$i]][] = $i;//делаем массив индексов на случай, если в изначальном массиве будут повторяющиеся элементы
        }
        for($i = 0; $i < $countNums - 1; $i++) {
            $diff = $target - $nums[$i];
            if (isset($hash[$nums[$diff]])) {
                foreach($hash[$nums[$diff]] as $num => $index) {
                    if ($index != $i) {
                        return [$i, $index];
                    }
                }
            }
        }
    }
}