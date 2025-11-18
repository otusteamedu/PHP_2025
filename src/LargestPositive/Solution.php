<?php
declare(strict_types=1);


namespace NikolayVanzhin\Php2025\LargestPositive;

class Solution
{

    /**
     * @param Integer[] $nums
     * @return Integer
     */
    function findMaxK(array $nums): int
    {
        $hash = array_flip($nums);
        $ans = -1;

        foreach ($hash as $key => $value) {
            if ($key > 0 && isset($hash[-$key])) {
                if ($ans < $key)
                    $ans = $key;
            }
        }

        return $ans;
    }
}