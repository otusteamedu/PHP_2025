<?php

// https://leetcode.com/problems/sort-array-by-increasing-frequency/description/

// O(n^2)

namespace two;

class Solution
{
    /**
     * @param Integer[] $a
     * @return Integer[]
     */
    function frequencySort(array $a): array
    {
        $frequency = [];

        foreach ($a as $number) {
            if (array_key_exists($number, $frequency)) {
                $frequency[$number]++;
            } else {
                $frequency[$number] = 1;
            }
        }

        $min = PHP_INT_MAX;
        $max = PHP_INT_MIN;

        foreach ($frequency as $count) {
            if ($count < $min) $min = $count;
            if ($count > $max) $max = $count;
        }

        $result = [];

        for ($index = $min; $index <= $max; $index++) {
            $numbers = [];

            foreach ($frequency as $number => $count) {
                if ($count === $index) {
                    $numbers[] = $number;
                }
            }

            $length = count($numbers);

            for ($i = 0; $i < $length - 1; $i++) {
                for ($j = 0; $j < $length - $i - 1; $j++) {
                    $a = $numbers[$j];
                    $b = $numbers[$j + 1];

                    if ($a < $b) {
                        $numbers[$j] = $b;
                        $numbers[$j + 1] = $a;
                    }
                }
            }

            foreach ($numbers as $number) {
                for ($i = 0; $i < $index; $i++) {
                    $result[] = $number;
                }
            }
        }

        return $result;
    }
}

print_r(new Solution()->frequencySort([
    2,
    3,
    1,
    3,
    2,
]));
