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
        $ans =[];
        $copy = $nums;
        sort($copy);

        foreach ($copy as $key => $value) {
            if (!isset($map[$value])) {
                $map[$value] = $key;
            }
        }
        foreach ($nums as $key => $value) {
            $ans[]= $map[$value];
        }

        return $ans;
    }
}