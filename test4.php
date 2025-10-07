<?php

class Solution {

    /**
     * @param Integer[] $nums
     * @return Integer
     */
    function findMaxK(array $nums): int
    {
        $set = array_flip($nums);
        $maxK = -1;

        foreach ($nums as $num) {
            if ($num > 0 && isset($set[-$num])) {
                $maxK = max($maxK, $num);
            }
        }

        return $maxK;
    }
}

