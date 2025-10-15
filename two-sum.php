<?php

/**
 * Линейная сложность.
 * 
 * Проходим по массиву и заполняем хеш - O(n).
 * 
 * Проход по хешу.
 */
function twoSum($nums, $target)
{
    $hash = [];
    for ($i = 0; $i < count($nums); $i++) {
        if (isset($hash[$nums[$i]]))
            $hash[$nums[$i]][] = $i;
        else
            $hash[$nums[$i]] = [$i];
    }
    foreach ($hash as $value => $indexes) {
        if ($value * 2 === $target && count($indexes) > 1)
            return $indexes;
        if (isset($hash[$target - $value]) && ($target - $value) !== $value)
            return [...$hash[$target - $value], ...$indexes];
    }
}
