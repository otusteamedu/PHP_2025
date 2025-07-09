<?php
// Largest Positive Integer That Exists With Its Negative
// простой перебор массива - сложность O(n)
class Solution {
    function findMaxK($nums) {
        $res = -1;
        $hash = [];
        
        foreach ($nums as $num) {
            if (isset($hash[-$num])) {
                if (abs($num) > $res) {
                    $res = abs($num);
                }
            }
            $hash[$num] = 1;
        } 

        return $res; 
    }
}