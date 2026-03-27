<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Ak\Hw\Services\Solution;
use Ak\Hw\Services\Solution2;
use Ak\Hw\Services\ListNode;

$s1 = new Solution();

try {
    $commonTail = new ListNode(8);
    $commonTail->next = new ListNode(4);
    $commonTail->next->next = new ListNode(5);

    $headA = new ListNode(4);
    $headA->next = new ListNode(1);
    $headA->next->next = $commonTail;

    $headB = new ListNode(5);
    $headB->next = new ListNode(6);
    $headB->next->next = new ListNode(1);
    $headB->next->next->next = $commonTail;


    $test = $s1->getIntersectionNode($headA, $headB);

    echo "Intersected at  $test->val ";

} catch (Exception $e) {
    pr($e->getMessage(), true, true);
}


$s2 = new Solution2();
echo $s2->fractionToDecimal(1, 2) . PHP_EOL;
echo $s2->fractionToDecimal(2, 1) . PHP_EOL;
echo $s2->fractionToDecimal(4, 333) . PHP_EOL;