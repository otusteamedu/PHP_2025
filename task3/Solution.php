<?php
// Intersection of Two Arrays
// два линейных перебора массива - сложность O(n)
class Solution {
    function intersection($nums1, $nums2) {
        $hash = [];
        foreach ($nums1 as $num) {
            $hash[$num] = 1;
        }

        $result = [];
        foreach ($nums2 as $num) {
            if (isset($hash[$num]) && $hash[$num] == 1) {
                $result[] = $num;
                $hash[$num] = 2;
            }
        }

        return $result;
    }
}