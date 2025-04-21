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
    {//brute force
        $answer = [];
        $hash = [];
        foreach ($nums as $key => $num) {
            $count = 0;
            foreach ($nums as $key1 => $num1) {
                if ($key1 === $key) {
                    continue;
                }
                if ($num > $num1) {
                    $count++;
                }
            }
            $answer[] = $count;

        }

        return $answer;
    }
}