<?php
declare(strict_types=1);


namespace NikolayVanzhin\Php2025\TwoSum;

class Solution
{

    /**
     * @param Integer[] $nums
     * @param Integer $target
     * @return Integer[]
     */
    function twoSum(array $nums, int $target): array
    {
        $answer = [];
        $hash = [];
        foreach ($nums as $key => $num) {
            $difference = $target - $num;
            if ($hash[$difference] ?? null) {
                return [$hash[$difference], $key];
            } else {
                $hash[$difference] = $key;
            }
        }

        return $answer;
    }
}