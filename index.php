<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use SergeyGolovanov\Php2025Hw7\Domain\NodeList;
use SergeyGolovanov\Php2025Hw7\App\Solution;

//Example1
//Input: list1 = [], list2 = []
//Output: []
//$arr1 = [];
//$arr2 = [];

//Example2
//Input: list1 = [], list2 = [0]
//Output: [0]
//$arr1 = [];
//$arr2 = [0];

//Example3
//Input: list1 = [], list2 = [0]
//Output: [0]
//$arr2 = [1, 2];
//$arr1 = [0];

//Example4
//Input: list1 = [1,2,4], list2 = [1,3,4]
//Output: [1,1,2,3,4,4]
//$arr1 = [1, 2, 4];
//$arr2 = [1, 3, 4];

//Example5
//Input: list1 = [-4, -2, -1], list2 = [-4, -3, -1]
//Output: [-4, -4, -3, -2, -1, -1]
$arr1 = [-4, -2, -1];
$arr2 = [-4, -3, -1];

$nodeList1 = new NodeList();
foreach ($arr1 as $value) {
	$nodeList1->append($value);
}

$nodeList2 = new NodeList();
foreach ($arr2 as $value) {
	$nodeList2->append($value);
}

$solution = new Solution();
$mergedNodeListHead = $solution->mergeTwoLists($nodeList1, $nodeList2);

var_export($mergedNodeListHead);