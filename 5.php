<?php

class Solution
{
    /**
     * @param int[] $nums
     * @return int[]
     */
    function smallerNumbersThanCurrent($nums)
    {
        $maxNumber = 100;
        $result = [];
        $hash = array_fill(0, $maxNumber + 1, 0);

        foreach ($nums as $num) {
            $hash[$num]++;
        }

        for ($i = 1; $i <= $maxNumber; $i++) {
            $hash[$i] += $hash[$i - 1];
        }

        foreach ($nums as $num) {
            $result[] = $hash[$num - 1] ?? 0;
        }

        return $result;
    }
}

// Сложность O(n) проходим по массиву двумя циклами

// Пример использования:
$solution = new Solution();

$result1 = $solution->smallerNumbersThanCurrent([8, 1, 2, 2, 3]);
$result2 = $solution->smallerNumbersThanCurrent([6, 5, 4, 8]);
$result3 = $solution->smallerNumbersThanCurrent([7, 7, 7, 7]);

print_r($result1); // [4,0,1,1,3]
echo PHP_EOL;
print_r($result2); // [2,1,0,3]
echo PHP_EOL;
print_r($result3); // [0,0,0,0]
