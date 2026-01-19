<?php

/**
 * Здесь временная сложность: создание хэш-таблицы, проверка + цикл по элементам
 */

class Solution {

    /**
     * @param Integer[] $nums
     * @return Integer
     */
    function findMaxK($nums) {
       $exists = array_flip($nums);
        $result = -1;
        
        // Используем array_keys для получения значений
        foreach (array_keys($exists) as $num) {
            if ($num > 0 && isset($exists[-$num])) {
                $result = max($result, $num);
            }
        }
        
        return $result;
    }

}