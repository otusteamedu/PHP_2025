<?php

// Алгоритмическая сложность О(n+m)

declare(strict_types=1);

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
    function getIntersectionNode($headA, $headB) {

        $a = $headA;
        $b = $headB;

        while ($a !== $b) {
            if ($a === null) {
                $a = $headB;
            } else {
                $a = $a->next;
            }

            if ($b === null) {
                $b = $headA;
            } else {
                $b = $b->next;
            }
        }

        return $a;
    }
}