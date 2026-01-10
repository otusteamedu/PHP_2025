<?php

/**
 * Линейная сложность.
 * 
 * Создаем хеш из входящего массива. Проходим по нему и добавляем только те элементы которые подходят под условие. abs($num) скорее всего O(1) - там вроде бы просто сдвиг байта идет?..
 */

function findMaxK($nums)
{
    $hash = [];

    foreach ($nums as $n) {
        $hash[$n] = true;
    }

    $max = 0;
    foreach ($hash as $num => $val) {
        if ($num > $max && isset($hash[0 - abs($num)]))
            $max = $num;
    }
    return $max ? $max : -1;
}
