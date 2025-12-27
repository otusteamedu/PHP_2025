<?php
function findMaxK($nums) {
    $positiveSet = [];
    $maxK = -1;

    foreach ($nums as $num) {
        if ($num > 0) {
            $positiveSet[$num] = true;
        }
    }

    foreach ($nums as $num) {
        if ($num < 0 && isset($positiveSet[-$num])) {
            $maxK = max($maxK, -$num);
        }
    }

    return $maxK;
}

echo findMaxK([-1, 2, -3, 3]);
echo findMaxK([-1, 10, 6, 7, -7, 1]);
echo findMaxK([-10, 8, 6, 7, -2, -3]);
