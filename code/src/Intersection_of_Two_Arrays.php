<?php

//Сложность решения O(n)
class Solution {

    /**
     * @param Integer[] $nums1
     * @param Integer[] $nums2
     * @return Integer[]
     */
    function intersection($nums1, $nums2) {
        $result = [];
        $hash1 = [];
        $hash2 = [];
        $countNums2 = count($nums2);
        for($i = 0; $i < $countNums2; $i++) {
            $hash2[$nums2[$i]]++;
        }

        $countNums1 = count($nums1);
        for($i = 0; $i < $countNums1; $i++) {
            $hash1[$nums1[$i]]++;
            if (isset($hash2[$nums1[$i]]) && $hash1[$nums1[$i]] == 1) {
                $result[] = $nums1[$i];
            }
        }

        return $result;
    }
}