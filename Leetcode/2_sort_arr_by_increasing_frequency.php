<?php

declare(strict_types=1);

/**
 * @param Integer[] $nums
 * @return Integer[]
 *
 *  Временная сложность: O(n log n)
 *  Память: O(n)
 */
function frequencySort(array $nums): array
{
    $frequency = array_count_values($nums);

    usort($nums, static function ($a, $b) use ($frequency) {
       $compare = $frequency[$a] <=> $frequency[$b];
       if ($compare !== 0) {
           return $compare;
       }

       return $b <=> $a;
    });

    return $nums;
}
