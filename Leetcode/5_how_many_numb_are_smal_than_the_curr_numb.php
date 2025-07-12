<?php

declare(strict_types=1);

/**
 * @param Integer[] $nums
 * @return Integer[]
 *
 *  Временная сложность: O(n log n)
 *  Память: O(n)
 */
function smallerNumbersThanCurrent(array $nums): array
{
    $sorted = $nums;
    sort($sorted);

    $map = [];
    foreach ($sorted as $index => $num) {
        if (!array_key_exists($num, $map)) {
            $map[$num] = $index;
        }
    }

    $result = [];
    foreach ($nums as $num) {
        $result[] = $map[$num];
    }

    return $result;
}
