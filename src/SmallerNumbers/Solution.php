<?php
declare(strict_types=1);


namespace NikolayVanzhin\Php2025\SmallerNumbers;

class Solution
{

    /**
     * @param Integer[] $nums
     * @return Integer[]
     */
    function smallerNumbersThanCurrent(array $nums): array
    {
        $map = [];
        $copy = $nums;
        sort($copy);

        foreach ($copy as $key => $value) {
            if (!isset($map[$value])) {
                $map[$value] = $key;
            }
        }

        array_walk($nums, function (&$value) use (&$map) {
            $value = $map[$value];
        });

        return $nums;
    }
}