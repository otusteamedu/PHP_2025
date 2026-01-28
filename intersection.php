<?php
class Solution {

    /**
     * https://leetcode.com/problems/intersection-of-two-arrays/description/
     *
     * @param Integer[] $nums1
     * @param Integer[] $nums2
     * @return Integer[]
     */
    function intersection($nums1, $nums2) {
        $tmp = [];
        $result = [];

        if (empty($nums1)) {
            return $result;
        }

        if (empty($nums2)) {
            return $result;
        }

        for ($i = 0; $i < count($nums1); $i++) {
            $number = $nums1[$i];
            if (!isset($tmp[$number])) {
                $tmp[$number] = true;
            }
        }

        for ($i = 0; $i < count($nums2); $i++) {
            $number = $nums2[$i];
            if (isset($tmp[$number])) {
                $result[] = $number;
                unset($tmp[$number]);
            }
        }

        return $result;
    }
}
//сложность O(n+m)
var_dump((new Solution)->intersection([4,9,5], [9,4,9,8,4]));