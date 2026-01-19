<?php

/**
 * Здесь идет временная сложность: подсчет частот, сортировка и сравнение
 */

class Solution {

    /**
     * @param Integer[] $nums
     * @return Integer[]
     */

    function frequencySort($nums) {

        // Считаем сколько раз встречается каждый элемент
        $count = [];
        foreach ($nums as $num) {
            $count[$num] = ($count[$num] ?? 0) + 1;
        }
        
        // Сортируем по правилу: частота по возрастанию, значение по убыванию
        usort($nums, function($a, $b) use ($count) {

            // Если частоты разные - сортируем по частоте
            if ($count[$a] !== $count[$b]) {
                return $count[$a] - $count[$b];
            }

            // Если частоты одинаковые - сортируем по значению наоборот
            return $b - $a;

        });
        
        return $nums;
    }
}