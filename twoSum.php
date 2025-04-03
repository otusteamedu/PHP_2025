<?php


//Input: nums = [2,7,11,15], target = 9
//Output: [0,1]
//Explanation: Because nums[0] + nums[1] == 9, we return [0, 1].


//$nums = [2,7,11,15];
//$target = 9;
//
//pr_debug("Изначальный массив");
//pr_debug($nums);
//
//pr_debug("Целевое число");
//pr_debug($target);
//
//pr_debug("------------------------------");
//
////pr_debug($arNums);
//
//$output = twoSum($nums, $target);
//pr_debug($output);
//TODO работает

function twoSum($nums, $target) {
    $arOutput = [];
    foreach ($nums as $index => $val) {
        $baseNumber = $nums[$index];

        //pr_debug($baseNumber);

        foreach ($nums as $index2 => $val2) {
            if ($index2 !== $index) {
                $plusNumber = $nums[$index2];

                if (($baseNumber + $plusNumber) == $target) {

                    //pr_debug($index);

                    $arOutput[] = $index;
                    $arOutput[] = $index2;
                    return $arOutput;
                }
            }
        }
    }

    return [];
}



function pr_debug($var) {
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}