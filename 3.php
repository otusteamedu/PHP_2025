<?php

// https://leetcode.com/problems/intersection-of-two-arrays/

// O(a + b)

namespace three;

class Solution
{
    /**
     * @param Integer[] $a
     * @param Integer[] $b
     * @return Integer[]
     */
    function intersection(array $a, array $b): array
    {
        $bf = [];

        foreach ($b as $number) {
            $bf[$number] = $number;
        }

        $i = [];

        foreach ($a as $number) {
            if (array_key_exists($number, $bf)) {
                $i[$number] = $number;
            }
        }

        return $i;
    }
}

print_r(new Solution()->intersection([4, 9, 5, 4], [9, 4, 9, 8, 4]));
