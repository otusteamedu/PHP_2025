<?php

/**
 * 
 * Быстрая сортировка с модифицированным условием - O(n log n).
 * 
 * n - нужно пройтись по всем элементам массива, log n - среднее количество таких циклов
 */

function frequencySort($nums, bool $twins = false)
{
    $count = array_count_values($nums);

    if (count($nums) <= 1)
        return $nums;
    if (count($nums) === 2) {
        if ($twins) {
            if ($nums[0] < $nums[1]) {
                $x = $nums[1];
                $nums[1] = $nums[0];
                $nums[0] = $x;
            }
        } else {
            if ($count[$nums[0]] === $count[$nums[1]] && $nums[0] > $nums[1]) {
                $x = $nums[1];
                $nums[1] = $nums[0];
                $nums[0] = $x;
            }
        }

        return $nums;
    }

    $f = false;
    $x = $nums[0];

    foreach ($nums as  $n) {
        if ($n !== $x)
            $f = true;
    }

    $pivot = $twins ? $nums[(int)count($nums) / 2] : $count[$nums[(int)count($nums) / 2]];
    $left = $right = $equals = [];
    for ($i = 0; $i < count($nums); $i++) {
        if ($twins) {
            if ($nums[$i] > $pivot)
                $left[] = $nums[$i];
            if ($nums[$i] === $pivot)
                $equals[] = $nums[$i];
            if ($nums[$i]  < $pivot)
                $right[] = $nums[$i];
        } else {
            if ($count[$nums[$i]] < $pivot)
                $left[] = $nums[$i];
            if ($count[$nums[$i]] === $pivot)
                $equals[] = $nums[$i];
            if ($count[$nums[$i]]  > $pivot)
                $right[] = $nums[$i];
        }
    }

    return array_merge($this->frequencySort($left), $f ? $this->frequencySort($equals, true) : $equals, $this->frequencySort($right));
}