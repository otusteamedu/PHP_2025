<?php

/**
 * Definition for a singly-linked list.
 * class ListNode {
 *     public $val = 0;
 *     public $next = null;
 *     function __construct($val = 0, $next = null) {
 *         $this->val = $val;
 *         $this->next = $next;
 *     }
 * }
 */

//https://leetcode.com/problems/merge-two-sorted-lists/

//class Solution {
//    /**
//     * @param ListNode $list1
//     * @param ListNode $list2
//     * @return ListNode
//     */
//    function mergeTwoLists($list1, $list2) {
//
//    }
//}


$list1 = [1,2,4];

$list2 = [1,3,4];

//O(n)
foreach ($list2 as $el) {
    $list1[] = $el;
}

$items = $list1;
//pr_debug($items);


$low = 0;
$high = count($items) - 1;

quickSort($items, 0, count($items) - 1);

pr_debug($items);

//pr_debug($items);

function quickSort(&$arr, $low, $high) {
    $i = $low;
    $j = $high;
    $middleIndex = ( $low + $high ) / 2;
    $middle = $arr[ $middleIndex ];  // middle - опорный элемент; в нашей реализации он находится посередине между low и high

    //pr_debug($middle);

    do {
        while($arr[$i] < $middle) ++$i;  // ищем элементы для правой части
        while($arr[$j] > $middle) --$j;  // ищем элементы для левой части

        if ($i <= $j) {
            // перебрасываем элементы
            $temp = $arr[$i];
            $arr[$i] = $arr[$j];
            $arr[$j] = $temp;
            // следующая итерация
            $i++; $j--;
        }
    }
    while($i < $j);

    if($low < $j){
        // рекурсивно вызываем сортировку для левой части
        quickSort($arr, $low, $j);
    }

    if($i < $high){
        // рекурсивно вызываем сортировку для правой части
        quickSort($arr, $i, $high);
    }
}


function pr_debug($var)
{
    $bt = debug_backtrace();
    $bt = $bt[0];
    ?>
    <div style='font-size:9pt; color:#000; background:#fff; border:1px dashed #000;'>
        <div style='padding:3px 5px; background:#99CCFF; font-weight:bold;'>File: <?= $bt["file"] ?>
            [<?= $bt["line"] ?>]
        </div>
        <?
        if ($var === 0) {
            echo '<pre>пусто</pre>';
            var_dump($var);
        } else {
            echo '<pre>';
            print_r($var);
            echo '</pre>';
        }
        ?>
    </div>
    <?
}