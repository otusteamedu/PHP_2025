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
        $hash = [];
        $answer = [];
        foreach ($nums as $num) {
            if (!isset($hash[$num])) {
                $hash[$num] = 0;
            }
            $hash[$num]++;
        }
$answer = [];
        foreach ($hash as $num => $count) {
            $answer[$count][] = $num;
        }
        for($i = 1; $i < count($answer); $i++){
            var_dump($answer[$i]);
            die;
        }

        return $answer;

    }
}