<?php

/**
 * 
 * Подсчет количества элементов O(n) - можно создать хеш и просто прибавлять значение.
 * 
 * Два цикла - это O(n^2) независимо от сдвига $j=$i
 * 
 * В итоге: квадратичная сложность
 */

function frequencySort($nums)
{
    $count = array_count_values($nums);

    for ($i = 0; $i < count($nums) - 1; $i++) {
        for ($j = $i; $j < count($nums); $j++) {
            if ($count[$nums[$i]] >= $count[$nums[$j]]) {
                $x = $nums[$i];
                $nums[$i] = $nums[$j];
                $nums[$j] = $x;
            }
        }
    }

    return $nums;
}
