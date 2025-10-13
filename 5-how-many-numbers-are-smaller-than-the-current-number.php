<?php

class Solution {

    /**
     * https://leetcode.com/problems/how-many-numbers-are-smaller-than-the-current-number/description/
     * @param Integer[] $nums
     * @return Integer[]
     */
    function smallerNumbersThanCurrent($nums) {
        $hash = [];
        //Создаем массив из 101 элемента со значением 0
        $hash = array_fill(0, 100, 0);
        foreach ($nums as $number) {
            $hash[$number]++;
        }
        //Проходим массив частот и кладем в значения массива значение счетчика - число чисел меньше числа ключа 
        $counter = 0;
        for ($j = 0; $j <= 100; $j++) {
            if ($hash[$j] === 0) {
                continue;
            }
            $counterIncr = $hash[$j];
            $hash[$j] = $counter;
            $counter += $counterIncr;
        }

        //Собираем результат
        $result = [];
        foreach ($nums as $number) {
            $result[] = $hash[$number];
        }
        return $result;
    }
}