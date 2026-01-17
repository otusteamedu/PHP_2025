<?php

// https://leetcode.com/problems/two-sum/description/

// O(n)

namespace one;

class Solution
{
    /**
     * @param Integer[] $a
     * @param Integer $target
     * @return Integer[]
     */
    function twoSum(array $a, int $target): array
    {
        $list = [];

        foreach ($a as $index => $number) {
            $source = $target - $number;

            if (array_key_exists($source, $list)) {
                return [$list[$source], $index];
            } else {
                $list[$number] = $index;
            }
        }

        return [];
    }
}

print_r(new Solution()->twoSum([2, 7, 11, 15], 22));
print_r(new Solution()->twoSum([2, 7, 11, 15], 30));
