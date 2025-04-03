<?php

//Input: nums = [1,1,2,2,2,3]
//Output: [3,1,1,2,2,2]
//Explanation: '3' has a frequency of 1, '1' has a frequency of 2, and '2' has a frequency of 3.

pr_debug("Изначальный массив");
$nums = [1,1,1,2,2,2,3];
pr_debug($nums);

$arFrequencies = [];
foreach ($nums as $val) {
    @$arFrequencies[$val]++;
}

//сортировка массива частот

//pr_debug($arFrequencies);

//TODO подумать как лучше сделать
asort($arFrequencies);

//pr_debug($arFrequencies);
//
//for ($i = 0; $i < count($arFrequencies); $i++) {
//    pr_debug($arFrequencies[$i]);
//
//    break;
//}

exit();


$arOutput = [];

//$minMinFrequency = min($arFrequencies);
//$numberWhichHasMinFrequency = array_search($minMinFrequency, $arFrequencies);

//добавляем в массив число нужное кол-во раз
foreach ($arFrequencies as $number => $numberFrequency) {
    for ($i = 0; $i < $numberFrequency; $i++) {
        $arOutput[] = $number;
    }
}


pr_debug($arOutput);




//
//
//pr_debug($arOutput);






//foreach ($arFrequencies as $number => $numberFrequency) {
//    pr_debug('$numberFrequency');
//    pr_debug($numberFrequency);
//
//    foreach ($arFrequencies as $number2 => $numberFrequency2) {
//        if ($number !== $number2) {
//            pr_debug('$numberFrequency2');
//            pr_debug($numberFrequency2);
//
//            //$plusNumber = $nums[$index2];
////            if (($baseNumber + $plusNumber) == $target) {
////
////                //pr_debug($index);
////
////                $arOutput[] = $index;
////                $arOutput[] = $index2;
////                return $arOutput;
////            }
//        }
//    }
//
//    //break;
//}

//pr_debug($arFrequencies);
//pr_debug(min($arFrequencies));


//exit();

//$minMinFrequency = min($arFrequencies);
//$numberWhichHasMinFrequency = array_search($minMinFrequency, $arFrequencies);
//
////pr_debug($minMinFrequency);
////pr_debug($numberWhichHasMinFrequency);
//
//$arOutput = [];
//for ($i = 0; $i < $minMinFrequency; $i++) {
//    $arOutput[] = $numberWhichHasMinFrequency;
//}
//
//pr_debug($arOutput);





function pr_debug($var) {
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}