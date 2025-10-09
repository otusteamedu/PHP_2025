<?php

/**
 * Квадратичная сложность.
 * 
 * Проход по двум циклам.
 */
function twoSum($nums, $target)
{
    for ($i = 0; $i < count($nums) - 1; $i++) {
        for ($j = $i + 1; $j < count($nums); $j++) {
            if (($nums[$i] + $nums[$j]) === $target)
                return [$i, $j];
        }
    }
}
