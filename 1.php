<?php

class Solution {

    /**
     * @param Integer[] $nums
     * @param Integer $target
     * @return Integer[]
     */
    function twoSum(array $nums, int $target): array {
        $hash = [];

        foreach ($nums as $key => $num) {
            if (isset($hash[$num])) {
                return [$hash[$num], $key];
            }

            $dif = $target - $num;
            $hash[$dif] = $key;
        }

        return [];
    }
} //сложность o(n) - один foreach