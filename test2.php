<?php

class Solution
{

    /**
     * @param Integer[] $nums
     * @return Integer[]
     */
    function frequencySort(array $nums): array
    {
        $freq = array_count_values($nums);

        usort($nums, function($a, $b) use ($freq) {
            if ($freq[$a] === $freq[$b]) {
                return $b - $a;
            }
            return $freq[$a] - $freq[$b];
        });

        return $nums;

    }
}

$solution = new Solution();
var_dump($solution->frequencySort([4, 4, 4, 1, 1, 2]));
