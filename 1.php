<?php

class Solution
{
    /**
     * @param int[] $nums
     * @param int $target
     * @return int[]
     */
    function twoSum($nums, $target)
    {
        $hash = [];

        foreach ($nums as $index => $value) {
            $diff = $target - $value;
            if (isset($hash[$diff])) {
                return [$hash[$diff], $index];
            }

            $hash[$value] = $index;
        }
    }
}

// Сложность O(n) проходим по массиву одним циклом

// Пример использования:
$solution = new Solution();

$result1 = $solution->twoSum([2, 7, 11, 15], 9);
$result2 = $solution->twoSum([3, 2, 4], 6);
$result3 = $solution->twoSum([3, 3], 6);

print_r($result1); // [0, 1]
echo PHP_EOL;
print_r($result2); // [1, 2]
echo PHP_EOL;
print_r($result3); // [0, 1]
