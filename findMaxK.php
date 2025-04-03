<?php

//Input: nums = [-1,2,-3,3]
//Output: 3
//Explanation: 3 is the only valid k we can find in the array.
//TODO работает

$nums = [-1,2,-3,3];

pr_debug(findMaxK($nums));

function findMaxK($nums) {
    $maxK = -1;
    foreach ($nums as $num) {
        if ($num > 0) {
            //проверка наличия противоположного числа в массиве $nums
            $oppositeNum = -1 * $num;
            if (in_array($oppositeNum, $nums)) {
                if ($num > $maxK) $maxK = $num;
            }
        }
    }
    return $maxK;
}




function pr_debug($var) {
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}