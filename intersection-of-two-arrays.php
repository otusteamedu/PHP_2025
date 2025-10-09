<?php

/**
 * 
 * Сначала идет фильтрация по уникальным элементам массивов. Сложность O(n) и O(m)
 * 
 * Затем проход по обоим массивам с операциями удаления O(1) и вставки O(1). Сложность O(n+m)
 * 
 * Проход по хешу - Сложность O(n) если все элементы пересекаются
 * 
 * В итоге: Сложность линейная O(n) (опускаем константы)
 */



function intersection($nums1, $nums2)
{
    $nums1 = array_unique($nums1);
    $nums2 = array_unique($nums2);

    $hash = [];

    while ($nums1 || $nums2) {
        $nums1 && $hash[array_pop($nums1)]++;
        $nums2 && $hash[array_pop($nums2)]++;
    }

    $arr = [];

    foreach ($hash as $key => $count) {
        $count > 1 && $arr[] = $key;
    }

    return $arr;
}
