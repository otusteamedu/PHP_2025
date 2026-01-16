<?php

// Сложность O(n)

/**
 * @param Integer[] $nums
 * @return Integer
 */
function findMaxK($nums) {
    $result = -1;
    $numsFlipped = array_flip($nums);

    foreach ($nums as $num) {
        if ($num > 0 && isset($numsFlipped[-$num])) {
            $result = max($result, $num);
        }
    }

    return $result;
}
