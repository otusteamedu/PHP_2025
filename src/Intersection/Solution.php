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
        $result = [];
        $hash = array_flip($nums1);
        foreach ($hash as $num => $value) {
            if (in_array($num, $nums2))
                $result[] = $num;
        }

        return $result;
    }
}