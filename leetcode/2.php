<?php

// Сложность O(n log n)

/**
 * @param Integer[] $nums
 * @return Integer[]
 */
function frequencySort($nums) {
    $frequency = array_count_values($nums);

    usort($nums, function($a, $b) use ($frequency) {
        if ($frequency[$a] === $frequency[$b]) {
            return $b <=> $a;
        }

        return $frequency[$a] <=> $frequency[$b];
    });

    return $nums;
}
