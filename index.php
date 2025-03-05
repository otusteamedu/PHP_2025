<?php

$list1 = [1,2,2,4,5];
$list2 = [1,3,4,6,10,12];

function merge(array $list1, array $list2)
{
    $result = [];
    $i = 0;
    $j = 0;

    while ($i < count($list1) && $j < count($list2)) {
        if ($list1[$i] < $list2[$j]) {
            $result[] = $list1[$i];
            $i++;
        } else {
            $result[] = $list2[$j];
            $j++;
        }
    }
    while ($i < count($list1)) {
        $result[] = $list1[$i];
        $i++;
    }
    while ($j < count($list2)) {
        $result[] = $list2[$j];
        $j++;
    }
    return $result;
}

print_r(merge($list1, $list2));