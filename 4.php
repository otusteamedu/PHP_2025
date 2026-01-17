<?php

// https://leetcode.com/problems/largest-positive-integer-that-exists-with-its-negative/description/

// O(n)

namespace four;

class Solution
{
    /**
     * @param Integer[] $a
     * @return Integer
     */
    function handle(array $a): int
    {
        $positive = $negative = [];

        foreach ($a as $number) {
            if ($number > 0) {
                $positive[$number] = $number;
            }

            if ($number < 0) {
                $negative[$number] = $number;
            }
        }

        $max = -1;

        foreach ($a as $number) {
            if (array_key_exists($number, $positive) && array_key_exists(-$number, $negative)) {
                if ($number > $max) {
                    $max = $number;
                }
            }
        }

        return $max;
    }
}

print_r(new Solution()->handle([-1, 2, -3, 3]));
