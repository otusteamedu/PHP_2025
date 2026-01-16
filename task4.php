<?php
// Решение задачи "Largest Positive Integer That Exists With Its Negative"
// Сложность: O(n)
function findMaxK($nums) {
    $hash = [];
    foreach ($nums as $num) {
        $hash[$num] = true;
    }
    
    $maxK = -1;
    foreach ($nums as $num) {
        if ($num > 0 && isset($hash[-$num]) && $num > $maxK) {
            $maxK = $num;
        }
    }
    
    return $maxK;
}

echo "Тест 1:\n";
$result = findMaxK([-1, 2, -3, 3]);
echo $result . "\n";

echo "\nТест 2:\n";
$result = findMaxK([-1, 10, 6, 7, -7, 1]);
echo $result . "\n";

echo "\nТест 3:\n";
$result = findMaxK([-10, 8, 6, 7, -2, -3]);
echo $result . "\n";
