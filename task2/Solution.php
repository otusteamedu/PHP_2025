<?php
// Sort Array by Increasing Frequency
// сложность перебора массива O(n)
// сложность встроенной функции сортировки usort O(n log n), используется алгоритм Hybrid Quicksort
// итоговая сложность O(n log n)
class Solution {
    function frequencySort($nums) {
        $hash = [];
        foreach ($nums as $num) {
            if (!isset($hash[$num])) {
                $hash[$num] = 0;
            }
            $hash[$num]++;
        }

         usort($nums, function($a, $b) use ($hash) {
            if ($hash[$a] != $hash[$b]) {
                return $hash[$a] - $hash[$b];
            }
            return $b - $a;
        });

        return $nums;
    }
}