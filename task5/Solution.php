<?php
// How Many Numbers Are Smaller Than the Current Number
// сложность встроенной функции сортировки sort O(n log n), используется алгоритм Introsort (Quicksort + Heapsort)
// два перебора массива - сложность O(n)
// итоговая сложность O(n log n)
class Solution {
    function smallerNumbersThanCurrent($nums) {
        $initial = $nums;
        sort($nums);

        $res = [];
        $hash = [];
        foreach ($nums as $key => $num) {
            if ($key > 0 && $num == $nums[$key - 1]) {
                $res[$key] = $res[$key - 1];
            } else {
                $res[$key] = $key;
            }
            $hash[$num] = $res[$key];
        }

        $result = [];
        foreach ($initial as $num) {
            $result[] = $hash[$num];
        }

        return $result;
    }
}