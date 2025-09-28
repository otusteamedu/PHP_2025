<?php

/**
 * Definition for a singly-linked list.
 * class ListNode {
 *     public $val = 0;
 *     public $next = null;
 *     function __construct($val) { $this->val = $val; }
 * }
 */

class Solution {
    /**
     * @param ListNode $headA
     * @param ListNode $headB
     * @return ListNode
     */
    function getIntersectionNode(?ListNode $headA, ?ListNode $headB): ?ListNode {
        if ($headA === null || $headB === null) {
            return null;
        }

        $a = $headA;
        $countA = 0;

        while ($a !== null) {
            $countA++;
            $a = $a->next;
        }

        $b = $headB;
        $countB = 0;

        while ($b !== null) {
            $countB++;
            $b = $b->next;
        }

        $a = $headA;
        $b = $headB;

        if ($countA > $countB) {
            while ($countA !== $countB) {
                $a = $a->next;
                $countA--;
            }
        }

        if ($countA < $countB) {
            while ($countA !== $countB) {
                $b = $b->next;
                $countB--;
            }
        }

        while ($a !== $b) {
            $a = $a->next;
            $b = $b->next;
        }

        return $a;
    }
} //сложность o(n+m), тк 2 раза проходимся по headA и 2 раза по headB