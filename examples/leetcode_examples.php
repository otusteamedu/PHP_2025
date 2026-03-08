<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Igor\Test\LeetCode\IntersectionOfTwoArrays;
use Igor\Test\LeetCode\LargestPositiveInteger;
use Igor\Test\LeetCode\TwoSum;
use Igor\Test\LeetCode\SortArrayByFrequency;

echo "=== Примеры использования решений LeetCode ===\n\n";

// ============================================
// 1. Intersection of Two Arrays
// ============================================
echo "1. Intersection of Two Arrays\n";
echo str_repeat("-", 50) . "\n";

$intersection = new IntersectionOfTwoArrays();

$nums1 = [1, 2, 2, 1];
$nums2 = [2, 2];
$result = $intersection->intersection($nums1, $nums2);

echo "nums1 = " . json_encode($nums1) . "\n";
echo "nums2 = " . json_encode($nums2) . "\n";
echo "Результат: " . json_encode($result) . "\n";
echo "Сложность: O(n + m) время, O(min(n, m)) память\n\n";

// ============================================
// 2. Largest Positive Integer
// ============================================
echo "2. Largest Positive Integer That Exists With Its Negative\n";
echo str_repeat("-", 50) . "\n";

$largest = new LargestPositiveInteger();

$nums = [-1, 2, -3, 3];
$result = $largest->findMaxK($nums);

echo "nums = " . json_encode($nums) . "\n";
echo "Результат: {$result}\n";
echo "Сложность: O(n) время, O(n) память\n\n";

// ============================================
// 3. Two Sum
// ============================================
echo "3. Two Sum\n";
echo str_repeat("-", 50) . "\n";

$twoSum = new TwoSum();

$nums = [2, 7, 11, 15];
$target = 9;
$result = $twoSum->twoSum($nums, $target);

echo "nums = " . json_encode($nums) . "\n";
echo "target = {$target}\n";
echo "Результат (индексы): " . json_encode($result) . "\n";
echo "Значения: nums[{$result[0]}] + nums[{$result[1]}] = {$nums[$result[0]]} + {$nums[$result[1]]} = {$target}\n";
echo "Сложность: O(n) время, O(n) память\n\n";

// ============================================
// 4. Sort Array by Increasing Frequency
// ============================================
echo "4. Sort Array by Increasing Frequency\n";
echo str_repeat("-", 50) . "\n";

$frequencySort = new SortArrayByFrequency();

$nums = [1, 1, 2, 2, 2, 3];
$result = $frequencySort->frequencySort($nums);

echo "nums = " . json_encode($nums) . "\n";
echo "Результат: " . json_encode($result) . "\n";
echo "Объяснение:\n";
echo "  - 3 встречается 1 раз\n";
echo "  - 1 встречается 2 раза\n";
echo "  - 2 встречается 3 раза\n";
echo "Сортировка: сначала по частоте (возрастание), при равной частоте - по значению (убывание)\n";
echo "Сложность: O(n log n) время, O(n) память\n\n";

echo "=== Примеры завершены ===\n";
