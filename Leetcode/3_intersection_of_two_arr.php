<?php

declare(strict_types=1);

/**
 * @param Integer[] $nums1
 * @param Integer[] $nums2
 * @return Integer[]
 *
 * Временная сложность: O(n + m)
 * Память: O(n + k)
 */
function intersection(array $nums1, array $nums2): array
{
    $map1 = [];
    foreach ($nums1 as $x) {
        $map1[$x] = true;
    }

    $map2 = [];
    foreach ($nums2 as $y) {
        if (isset($map1[$y])) {
            $map2[$y] = true;
        }
    }

    return array_keys($map2);
}
