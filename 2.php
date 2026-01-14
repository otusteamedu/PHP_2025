<?php

class Solution
{

    /**
     * @param int[] $nums
     * @return int[]
     */
    function frequencySort($nums)
    {
        $hash = [];

        foreach ($nums as $num) {
            if (!isset($hash[$num])) {
                $hash[$num] = 0;
            }

            $hash[$num]++;
        }

        usort($nums, function ($a, $b) use ($hash) {
            if ($hash[$a] === $hash[$b]) {
                return $b <=> $a;
            }

            return $hash[$a] <=> $hash[$b];
        });

        return $nums;
    }
}

// Example usage:
$solution = new Solution();

$result1 = $solution->frequencySort([1, 1, 2, 2, 2, 3]);
$result2 = $solution->frequencySort([2, 3, 1, 3, 2]);
$result3 = $solution->frequencySort([-1, 1, -6, 4, 5, -6, 1, 4, 1]);

print_r($result1); // [3,1,1,2,2,2]
echo PHP_EOL;
print_r($result2); // [1,3,3,2,2]
echo PHP_EOL;
print_r($result3); // [5,-1,4,4,-6,-6,1,1,1]
