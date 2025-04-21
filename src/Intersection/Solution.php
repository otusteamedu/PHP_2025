<?php
declare(strict_types=1);


namespace NikolayVanzhin\Php2025\Intersection;

class Solution
{

    /**
     * @param Integer[] $nums1
     * @param Integer[] $nums2
     * @return Integer[]
     */
    function intersection(array $nums1, array $nums2): array
    {
        $hash = [];
        $result = [];

        foreach($nums1 as $num) {
            $hash[$num] = 1;
        }

        foreach($nums2 as $num) {
            if ($hash[$num] === 1) {
                $result[] = $num;
                $hash[$num] = 0;
            }
        }

        return $result;
    }
}