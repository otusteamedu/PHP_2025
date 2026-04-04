<?php

class Solution {

    /**
     * @param Integer[] $nums1
     * @param Integer[] $nums2
     * @return Integer[]
     */
    function intersection(array $nums1, array $nums2): array {
        $hash = [];
        $result = [];

        foreach ($nums1 as $num1) {
            $hash[$num1]++;
        }

        foreach ($nums2 as $num2) {
            if (isset($hash[$num2])) {
                $result[] = $num2;
                unset($hash[$num2]);
            }
        }

        return $result;
    }
} //сложность o(n + m) - foreach по $nums1 и $nums2