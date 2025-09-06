<?php

//https://leetcode.com/problems/intersection-of-two-linked-lists/

class ListNode
{
    public $val = 0;
    public $next = null;

    function __construct($val)
    {
        $this->val = $val;
    }
}

class Solution
{
    /**
     * @param ListNode $headA
     * @param ListNode $headB
     * @return ListNode|null
     */
    function getIntersectionNode($headA, $headB): ?ListNode
    {
        $a = $headA;
        $b = $headB;

        while ($a !== $b) {
            if ($a !== null) {
                $a = $a->next;
            } else {
                $a = $headB;
            }

            if ($b !== null) {
                $b = $b->next;
            } else {
                $b = $headA;
            }
        }

        return $a;

    }
}

$common = new ListNode(5);

$head1 = new ListNode(1);
$head1->next = new ListNode(4);
$head1->next->next = $common;

$head2 = new ListNode(7);
$head2->next = $common;
$result = (new Solution())->getIntersectionNode($head1, $head2);
var_dump($result ? $result->val : null);
