<?php
class Solution {

    /**
     * https://leetcode.com/problems/how-many-numbers-are-smaller-than-the-current-number/description/
     *
     * @param Integer[] $nums
     * @return Integer[]
     */
    function smallerNumbersThanCurrent($nums) {
        $result = [];
        for ($i = 0; $i < count($nums); $i++) {
            for ($j = $i + 1; $j < count($nums); $j++) {
                if (!isset($result[$j])) {
                    $result[$j] = 0;
                }
                if (!isset($result[$i])) {
                    $result[$i] = 0;
                }
                if ($nums[$i] < $nums[$j]) {
                    $result[$j]++;
                }

                if ($nums[$i] > $nums[$j]) {
                    $result[$i]++;
                }
            }
        }
        ksort($result);
        return $result;
    }
}
//Сложность O(n^2).
echo '<pre>';
var_dump((new Solution)->smallerNumbersThanCurrent([8,1,2,2,3]));