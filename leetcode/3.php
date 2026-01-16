<?php

// Сложность O(n + m)

/**
 * @param Integer[] $nums1
 * @param Integer[] $nums2
 * @return Integer[]
 */
function intersection($nums1, $nums2) {
    $result = [];
    $numsFlipped = array_flip($nums1);

    foreach ($nums2 as $num) {
        if (isset($numsFlipped[$num])) {
            $result[$num] = true;
        }
    }

    return array_keys($result);
}
