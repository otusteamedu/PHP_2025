<?php

/**
 *  Использование хэш-таблицы, которое имеет линейную временную сложность,
 *  проходим по массиву всего один раз + каждая операция с хэш-таблицей, это вставка и поиск
 */

class Solution {
    /**
     * @param Integer[] $nums
     * @param Integer $target
     * @return Integer[]
     */
    
    function twoSum($nums, $target) {
        $map = []; // Хэш-таблица для хранения значений и их индексов
        
        for ($i = 0; $i < count($nums); $i++) {
            $complement = $target - $nums[$i];
            
            // Проверяем, есть ли дополнение уже в таблице
            if (isset($map[$complement])) {
                return [$map[$complement], $i];
            }
            
            // Сохраняем текущий элемент и его индекс
            $map[$nums[$i]] = $i;
        }
        
        return []; 
    }
}