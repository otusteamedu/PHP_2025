<?php
// Two Sum
// O(n) один цикл по массиву
class Solution {
    function twoSum($nums, $target) {
        $hash = [];
        foreach ($nums as $key => $num) {
            if (isset($hash[$num])) {
                return [$hash[$num], $key];
            }

            $hash[$target - $num] = $key; 
        }
        return [];
    }
}