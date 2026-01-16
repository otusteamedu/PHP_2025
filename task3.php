<?php
// Решение задачи "Intersection of Two Arrays"
// Сложность: O(n + m)
function intersection($nums1, $nums2) {
    $set1 = [];
    foreach ($nums1 as $num) {
        $set1[$num] = true;
    }
    
    $set2 = [];
    foreach ($nums2 as $num) {
        $set2[$num] = true;
    }
    
    $result = [];
    foreach ($set1 as $num => $exists) {
        if (isset($set2[$num])) {
            $result[] = $num;
        }
    }
    
    return $result;
}

echo "Тест 1:\n";
$result = intersection([1, 2, 2, 1], [2, 2]);
print_r($result);

echo "\nТест 2:\n";
$result = intersection([4, 9, 5], [9, 4, 9, 8, 4]);
print_r($result);
