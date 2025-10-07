<?php

class Solution {

    /**
     * @param Integer[] $nums1
     * @param Integer[] $nums2
     * @return Integer[]
     */
    function intersection(array $nums1, array $nums2): array
    {
        $set1 = array_unique($nums1);
        $set2 = array_unique($nums2);

        return array_values(array_intersect($set1, $set2));
    }
}
