<?php
require __DIR__ . '/vendor/autoload.php';

use Arlex2305k\MergeLists\{ListHelper, Solution};

$objSolution = new Solution();

// Test1
$list1 = ListHelper::createFromArray([1,2,4]);
$list2 = ListHelper::createFromArray([1,3,4]);
echo "--- Test1 ---\nList1: $list1\nList2: $list2\n";
$totalList = $objSolution->mergeTwoLists($list1, $list2);
echo "Total: $totalList\n\n";

// Test2
$list1 = ListHelper::createFromArray([]);
$list2 = ListHelper::createFromArray([]);
echo "--- Test2 ---\nList1: $list1\nList2: $list2\n";
$totalList = $objSolution->mergeTwoLists($list1, $list2);
echo "Total: $totalList\n\n";

// Test3
$list1 = ListHelper::createFromArray([]);
$list2 = ListHelper::createFromArray([0]);
echo "--- Test3 ---\nList1: $list1\nList2: $list2\n";
$totalList = $objSolution->mergeTwoLists($list1, $list2);
echo "Total: $totalList\n\n";
