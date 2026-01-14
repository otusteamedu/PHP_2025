<?php

class Solution
{
    /**
     * @param int[] $nums
     * @return int
     */
    function findMaxK($nums)
    {
        $hash = [];
        $max = -1;

        foreach ($nums as $num) {
            $hash[$num] = 1;

            if (isset($hash[-$num])) {
                $max = max($max, abs($num));
            }
        }

        return $max;
    }
}

// Example usage:
$solution = new Solution();

$result1 = $solution->findMaxK([-1, 2, -3, 3]);
$result2 = $solution->findMaxK([-1, 10, 6, 7, -7, 1]);
$result3 = $solution->findMaxK([-10, 8, 6, 7, -2, -3]);

print_r($result1); // 3
echo PHP_EOL;
print_r($result2); // 7
echo PHP_EOL;
print_r($result3); // -1
