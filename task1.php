<?php
// Решение задачи "Two Sum"
// Сложность: O(n)
function twoSum($nums, $target) {
    $hash = [];
    $countNums = count($nums);
    for ($i = 0; $i < $countNums; $i++) {
        $complement = $target - $nums[$i];
        if (array_key_exists($complement, $hash)) {
            return [$hash[$complement], $i];
        }
		$hash[$nums[$i]] = $i;
    }
    return [];
}

echo "Тест 1:\n";
$result = twoSum([2, 7, 11, 15], 9);
print_r($result);

echo "\nТест 2:\n";
$result = twoSum([3, 2, 4], 6);
print_r($result);

echo "\nТест 3:\n";
$result = twoSum([3, 3], 6);
print_r($result);
