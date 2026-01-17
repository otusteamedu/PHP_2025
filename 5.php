<?php

// https://leetcode.com/problems/how-many-numbers-are-smaller-than-the-current-number/

// O(n^2)

class Solution
{
    /**
     * @param Integer[] $a
     * @return Integer[]
     */
    function handle(array $a): array
    {
        $result = [];

        foreach ($a as $number) {
            $count[$number] = 0;

            foreach ($a as $value) {
                if ($number > $value) {
                    $count[$number]++;
                }
            }

            $result[] = $count[$number];
        }

        return $result;
    }
}

print_r(new Solution()->handle([8, 1, 2, 2, 3]));
