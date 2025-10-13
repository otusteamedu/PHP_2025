<?php

class Solution {

    /**
     * https://leetcode.com/problems/sort-array-by-increasing-frequency/description/
     * @param Integer[] $nums
     * @return Integer[]
     */
     function frequencySort($nums) {
        $hash = [];
        foreach ($nums as $number) {
        	if (!isset($hash[$number])) {
        		$hash[$number] = 0;
        	}
            $hash[$number]++; 
        }
        
        uksort($hash, function($a, $b) use($hash) {
        	if ($hash[$a] === $hash[$b]) {
        		return $b - $a;
        	}
            return $hash[$a] - $hash[$b];
        });

        //Собираем ответ
        $result = [];
        foreach ($hash as $number => $frequency) {
            for ($i = 0; $i < $frequency; $i++) {
                $result[] = $number;
            }
        }
        return $result;
    }
}