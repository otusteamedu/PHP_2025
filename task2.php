<?php
// Решение задачи "Sort Array by Increasing Frequency"
// Сложность: O(n log n) - из-за алгоритма быстрой сортировки в usort
function frequencySort($nums) {
    $freq = [];
    foreach ($nums as $num) {
        if (isset($freq[$num])) {
            $freq[$num]++;
        } else {
            $freq[$num] = 1;
        }
    }

    usort($nums, function($a, $b) use ($freq) {
        if ($freq[$a] != $freq[$b]) {
            return $freq[$a] - $freq[$b];
        } else {
			return $b - $a;
		}
    });
    
    return $nums;
}

echo "Тест 1:\n";
$result = frequencySort([1, 1, 2, 2, 2, 3]);
print_r($result);

echo "\nТест 2:\n";
$result = frequencySort([2, 3, 1, 3, 2]);
print_r($result);

echo "\nТест 3:\n";
$result = frequencySort([-1, 1, -6, 4, 5, -6, 1, 4, 1]);
print_r($result);
