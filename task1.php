<?php

declare(strict_types=1);

class Solution {

    /**
     * @param Integer[] $nums
     * @param Integer $target
     * @return Integer[]
     */
    public function twoSum($nums, $target)
    {

        $hash = [];
        foreach ($nums as $key => $num) {
            $hash[$num][] = $key;
        }

        foreach ($hash as $number => $indexes) {
            $n = $target - $number;
            if ($number == $n && count($indexes) >= 2) {
                return [$indexes[0], $indexes[1]];
            }

            if ($number != $n && isset($hash[$n])) {
                return [$indexes[0], $hash[$n][0]];
            }

        }
    }
}