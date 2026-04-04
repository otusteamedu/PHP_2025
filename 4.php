<?php

class Solution {

    /**
     * @param Integer[] $nums
     * @return Integer
     */
    function findMaxK(array $nums): int {
        $hash = [];
        $result = -1;

        foreach ($nums as $num) {
            $pos = $num < 0 ? $num * -1 : $num;

            if (isset($hash[$num]) && $result < $pos) {
                $result = $pos;

                continue;
            }

            $hash[$num * -1]++;
        }

        return $result;
    }
} //сложность o(n) - один foreach