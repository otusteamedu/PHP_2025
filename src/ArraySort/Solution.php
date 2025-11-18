<?php
declare(strict_types=1);


namespace NikolayVanzhin\Php2025\ArraySort;

class Solution
{

    /**
     * @param Integer[] $nums
     * @return Integer[]
     */
    function frequencySort(array $nums): array
    {
        $freq = [];
        foreach ($nums as $num) {
            if (!isset($freq[$num])) {
                $freq[$num] = 0;
            }
            $freq[$num]++;
        }
        usort($nums, function ($a, $b) use ($freq) {
            if ($freq[$a] == $freq[$b]) {
                return $a < $b;
            }
            return ($freq[$a] < $freq[$b]) ? -1 : 1;
        });

        return $nums;
    }
}