<?php

declare(strict_types=1);

// Задача 1
function twoSum($nums, $target): array
{
    $hash = [];

    foreach ($nums as $index => $num) {
        $hash[$num] = $index;
    }

    foreach ($nums as $index => $num) {
        $diff = $target - $num;

        if (isset($hash[$diff]) && $hash[$diff] !== $index) {
            return [$index, $hash[$diff]];
        }
    }

    return [];
}
// Задача 2
function frequencySort($nums)
{
    if (count($nums) === 1) {
        return $nums;
    }

    $hash  = [];

    foreach ($nums as $num) {
        if (!isset($hash[$num])) {
            $hash[$num] = 0;
        }
        $hash[$num]++;
    }

    usort($nums, function ($a, $b) use ($hash) {
        if ($hash[$a] < $hash[$b]) {
            return -1;
        } else if ($hash[$a] > $hash[$b]) {
            return 1;
        } else {
            return $b <=> $a;
        }
    });

    return $nums;
}

// Задача 3
function intersection($nums1, $nums2)
{
    $hash = [];
    $ans = [];

    foreach ($nums1 as $num) {
        $hash[$num] = false;
    }

    foreach ($nums2 as $num) {
        if (isset($hash[$num])) {
            $hash[$num] = true;
        }
    }

    foreach ($hash as $num => $val) {
        if ($val) {
            $ans[] = $num;
        }
    }

    return $ans;
}

// Задача 4
function findMaxK($nums)
{
    $max = -1;
    $hash = [];

    foreach ($nums as $num) {
        $hash[$num] = true;
    }

    foreach ($nums as $num) {
        if ($num > $max && isset($hash[-$num])) {
            $max = $num;
        }
    }

    return $max;
}

// Задача 5
function smallerNumbersThanCurrent($nums)
{
    $sorted = $nums;
    $hash = [];

    sort($sorted);

    foreach (array_unique($sorted) as $key => $num) {
        $hash[$num] = $key;
    }

    foreach ($nums as $key => $num) {
        $nums[$key] = $hash[$num];
    }

    return $nums;
}