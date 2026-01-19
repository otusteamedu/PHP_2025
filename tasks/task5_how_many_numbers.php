<?php

/**
 * Здесь используется временная сложность из-за копирования и сортировки массива
 * + пространственная: копия массива, использование хэш-таблицы и результаты
 */

class Solution {

    /**
     * @param Integer[] $nums
     * @return Integer[]
     */
    function smallerNumbersThanCurrent($nums) {
        
        // Копируем и сортируем массив
        $sorted = $nums;
        sort($sorted);
        
        $map = [];
        foreach ($sorted as $index => $value) {

            // Если число встречается впервые, сохраняем его индекс
            // Индекс в отсортированном массиве = количество меньших чисел
            if (!isset($map[$value])) {
                $map[$value] = $index;
            }
        }
        
        // Формируем результат
        $result = [];
        foreach ($nums as $num) {
            $result[] = $map[$num];
        }
        
        return $result;
    }
}