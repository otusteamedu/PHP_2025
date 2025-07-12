<?php

declare(strict_types=1);

/**
 * @param Integer[] $nums
 * @return Integer
 *
 *  Временная сложность: O(n)
 *  Память: O(n)
 */
function findMaxK(array $nums): int
{
    $seen = [];
    foreach ($nums as $x) {
        $seen[$x] = true;
    }

    $answer = -1;
    foreach ($nums as $x) {
        if ($x > 0 && isset($seen[-$x])) {
            if ($x > $answer) {
                $answer = $x;
            }
        }
    }

    return $answer;
}
