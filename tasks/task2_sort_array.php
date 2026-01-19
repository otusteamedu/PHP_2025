<?php

/**
 * Здесь идет временная сложность: подсчет частот, хэш-таблица, сортировка и сравнение
 */

class Solution {

    /**
     * @param Integer[] $nums
     * @return Integer[]
     */

    function frequencySort($nums) {

        // Подсчет частоты
        $count = [];
        foreach ($nums as $num) {
            $count[$num] = ($count[$num] ?? 0) + 1;
        }
        
        // Получаем все уникальные числа
        $uniqueNums = array_keys($count);
        
        // Сортируем уникальные числа по частоте и значению
        usort($uniqueNums, function($a, $b) use ($count) {
            if ($count[$a] !== $count[$b]) {
                return $count[$a] - $count[$b]; // Частота по возрастанию
            }
            return $b - $a; // Значение по убыванию
        });
        
        // Получаем результат
        $result = [];
        foreach ($uniqueNums as $num) {
            for ($i = 0; $i < $count[$num]; $i++) {
                $result[] = $num;
            }
        }
        
        return $result;
    }
}