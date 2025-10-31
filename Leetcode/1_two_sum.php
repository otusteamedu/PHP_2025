<?php

declare(strict_types=1);

/**
 * @param Integer[] $nums
 * @return Integer[]
 *
 *  Временная сложность: O(n)
 *  Память: O(n)
 */
function twoSum(array $nums, int $target): array
{
    $map = [];

    foreach ($nums as $i => $num) {
        $need = $target - $num;
        if (isset($map[$need])) {
            return [$map[$need], $i];
        }
        $map[$num] = $i;
    }

    return [];
}
