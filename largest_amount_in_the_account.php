<?php

declare(strict_types=1);

function maxPrefixSum(array $nums): int
{
    $len = count($nums);

    for ($i = 1; $i < $len; $i++) {
        $nums[$i] = $nums[$i - 1] + $nums[$i];
    }

    $ans = 0;

    foreach ($nums as $sum) {
        if ($sum > $ans) {
            $ans = $sum;
        }
    }

    return $ans;
}
