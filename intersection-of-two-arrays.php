<?php

/**
 * 
 * Заполняем хеш-таблицу для первого массива - O(n)
 * 
 * Проходим по второму массиву и ищем в хеше элементы из второго массива. Если есть, то меняем флаг. O(n).
 * 
 * Проход по хешу и проверка флагов для элементов - O(n).
 * 
 * В итоге: Сложность линейная O(n)
 */



function intersection($nums1, $nums2)
{
    $hash = [];

    for ($i = 0; $i < count($nums1); $i++)
        $hash[$nums1[$i]] = false;

    for ($i = 0; $i < count($nums2); $i++)
        if (isset($hash[$nums2[$i]]))
            $hash[$nums2[$i]] = true;

    $arr = [];

    foreach ($hash as $key => $val) {
        $val && $arr[] = $key;
    }

    return $arr;
}
