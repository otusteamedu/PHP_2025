<?php
declare(strict_types=1);

class ListNode
{
    public int $val = 0;
    public ?ListNode $next = null;

    function __construct(int $val)
    {
        $this->val = $val;
    }
}

class Solution
{
    function getIntersectionNode(?ListNode $headA, ?ListNode $headB): ?ListNode
    {
        if ($headA === null || $headB === null) {
            return null;
        }

        $tmpHeadA = $headA;
        $tmpHeadB = $headB;
        while ($tmpHeadA !== $tmpHeadB) {
            $tmpHeadA = ($tmpHeadA === null) ? $headB : $tmpHeadA->next;
            $tmpHeadB = ($tmpHeadB === null) ? $headA : $tmpHeadB->next;
        }

        return $tmpHeadA;
    }
}


$obListNode = new ListNode(4);
$obListNode->next = new ListNode(5);
$obListNodeCommon = new ListNode(8);
$obListNodeCommon->next = $obListNode;


$obListNodeA_1 = new ListNode(1);
$obListNodeA_1->next = $obListNodeCommon;
$headA = new ListNode(4);
$headA->next = $obListNodeA_1;


$obListNodeB_1 = new ListNode(1);
$obListNodeB_1->next = $obListNodeCommon;
$obListNodeB_2 = new ListNode(6);
$obListNodeB_2->next = $obListNodeB_1;
$headB = new ListNode(5);
$headB->next = $obListNodeB_2;


$head = (new Solution)->getIntersectionNode($headA, $headB);

if ($head !== null) {
    echo 'Intersected at ' . $head->val;
} else {
    echo 'No intersection';
}
