<?php

//https://leetcode.com/problems/two-sum/description/

class Solution
{
    /**
     * @param Integer[] $nums
     * @param Integer $target
     * @return Integer[]
     */
    function twoSum(array $nums, int $target): array
    {
        $newNums = array_filter($nums, function ($num) use ($target) {
            return $num < $target;
        });

        foreach ($newNums as $key => $num) {
            $result = $this->search($key, $num, $newNums, $target);
            if ($result !== null) {
                return $result;
            }
        }

        return [];
    }

    private function search($keyN, $num, $array, $target): ?array
    {
        unset($array[$keyN]);

        foreach ($array as $key => $value) {
            if ($num + $value === $target) {
                return [$keyN, $key];
            }
        }

        return null;
    }
}

$result = new Solution()->twoSum([2, 1, 3, 2], 5);
var_dump($result);
