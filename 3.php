<?php

class Solution
{
    /**
     * @param int[] $nums1
     * @param int[] $nums2
     * @return int[]
     */
    function intersection($nums1, $nums2)
    {
        $hash = [];
        $result = [];

        foreach ($nums1 as $num) {
            $hash[$num] = 1;
        }

        foreach ($nums2 as $num) {
            if (isset($hash[$num])) {
                $result[] = $num;
                unset($hash[$num]);
            }
        }

        return $result;
    }
}

// Example usage:
$solution = new Solution();

$result1 = $solution->intersection([1, 2, 2, 1], [2, 2]);
$result2 = $solution->intersection([4, 9, 5], [9, 4, 9, 8, 4]);

print_r($result1); // [2]
echo PHP_EOL;
print_r($result2); // [4,9]
