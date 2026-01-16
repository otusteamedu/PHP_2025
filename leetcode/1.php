<?php

// Сложность O(n)

/**
 * @param Integer[] $nums
 * @param Integer $target
 * @return Integer[]
 */
function twoSum($nums, $target) {
    $indexByValue = [];

    foreach ($nums as $index => $num) {
        $requiredNum = $target - $num;
        if (array_key_exists($requiredNum, $indexByValue)) {
            return [$indexByValue[$requiredNum], $index];
        }

        $indexByValue[$num] = $index;
    }

    return [];
}
