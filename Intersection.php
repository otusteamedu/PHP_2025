<?php

declare(strict_types=1);

class ListNode
{
    public int $val = 0;
    public ListNode|null $next = null;

    function __construct($val)
    {
        $this->val = $val;
    }
}

class Solution
{
    /**
     * @param ?ListNode $headA
     * @param ?ListNode $headB
     * @return ListNode|null
     */
    function getIntersectionNode(?ListNode $headA, ?ListNode $headB): ?ListNode
    {
        $a = $headA;
        $b = $headB;

        while ($a !== $b) {
            $a = $a === null ? $headB : $a->next;
            $b = $b === null ? $headA : $b->next;
        }

        return $a;
    }
}
