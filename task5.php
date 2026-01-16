<?php
// Решение задачи "How Many Numbers Are Smaller Than the Current Number"
// Сложность: O(n*n)
function smallerNumbersThanCurrent($nums) {
    $result = [];
    $countNums = count($nums);

    for ($i = 0; $i < $countNums; $i++) {
        $count = 0;
        for ($j = 0; $j < $countNums; $j++) {
            if ($nums[$j] < $nums[$i]) {
                $count++;
            }
        }
        $result[] = $count;
    }

    return $result;
}

echo "Тест 1:\n";
$result = smallerNumbersThanCurrent([8, 1, 2, 2, 3]);
print_r($result);

echo "\nТест 2:\n";
$result = smallerNumbersThanCurrent([6, 5, 4, 8]);
print_r($result);

echo "\nТест 3:\n";
$result = smallerNumbersThanCurrent([7, 7, 7, 7]);
print_r($result);
