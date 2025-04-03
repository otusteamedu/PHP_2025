<?php

//Example 1:
//
//Input: nums1 = [1,2,2,1], nums2 = [2,2]
//Output: [2]

//TODO работает

//
//$nums1 = [1,2,2,1];
//$nums2 = [2,2];
//
//
//pr_debug(intersection($nums1, $nums2));


//pr_debug($nums1);
//pr_debug($nums2);

function intersection($nums1, $nums2) {
    if (count($nums1) < count($nums2)) {
        $baseArray = $nums1;
        $searchArray = $nums2;
    } else {
        $baseArray = $nums2;
        $searchArray = $nums1;
    }

    $arOutput = [];
    foreach ($baseArray as $number) {
        if (in_array($number, $searchArray)) {
            $arOutput[] = $number;
        }
    }

    return $arOutput;
}

function pr_debug($var) {
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}