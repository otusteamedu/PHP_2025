<?php
declare(strict_types=1);


require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\ListNode;
use App\Solution;

$list1 = new ListNode(1, new ListNode(2, new ListNode(4)));

$list2 = new ListNode(1, new ListNode(3, new ListNode(4)));

$solution = new Solution();

$result = $solution->mergeTwoLists($list1, $list2);

while ($result->next != null) {
    echo $result->next->val . PHP_EOL;
    $result = $result->next;
}

die(0);
