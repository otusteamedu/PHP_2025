<?php

namespace LeetCode;

class ListNode
{
    public $val = 0;
    public $next = null;
    function __construct($val = 0, $next = null)
    {
        $this->val = $val;
        $this->next = $next;
    }
}

class IntersectionOfTwoLinkedLists
{
    /**
     * @param ListNode $headA
     * @param ListNode $headB
     * @return ListNode|null
     */
    public function getIntersectionNode($headA, $headB)
    {
        if ($headA === null || $headB === null) {
            return null;
        }

        $pA = $headA;
        $pB = $headB;

        while ($pA !== $pB) {
            $pA = ($pA === null) ? $headB : $pA->next;
            $pB = ($pB === null) ? $headA : $pB->next;
        }

        return $pA;
    }
}
