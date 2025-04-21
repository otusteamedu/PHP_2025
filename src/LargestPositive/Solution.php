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
        $negative = [];
        $ans = -1;
        foreach ($nums as $num) {
            if ($num < 0) {
                $negative[$num] = $num;
            }
        }
        foreach ($nums as $num) {
            if ($num > $ans && isset($negative[-$num])) {
                $ans = $num;
            }
        }

        return $ans;
    }
}