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
    function getIntersectionNode($headA, $headB) {
        $lenA = 1;
        $current = $headA;
        while ($current->next) {
            $lenA += 1;
            $current = $current->next;
        }

        $lenB = 1;
        $current = $headB;
        while ($current->next) {
            $lenB += 1;
            $current = $current->next;
        }

        $diff = $lenA - $lenB;

        $currentA = $headA;
        $currentB = $headB;
        $aMore = $diff > 0;
        $diff = abs($diff);
        for ($i = 0; $i < $diff; $i++) {
            if ($aMore) {
                $currentA = $currentA->next;
            } else {
                $currentB = $currentB->next;
            }
        }

        if ($currentA === $currentB) {
            return $currentA;
        }

        while ($currentA->next && $currentB->next) {
            $nodeA = $currentA->next;
            $nodeB = $currentB->next;

            if ($nodeA === $nodeB) {
                return $nodeA;
            }

            $currentA = $nodeA;
            $currentB = $nodeB;
        }

        return null;
    }
}