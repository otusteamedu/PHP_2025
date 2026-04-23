<?php

namespace Alisaselezneva\Code\Domain\Services;

class Solution {
    
    /**
     * @param ListNode $headA
     * @param ListNode $headB
     * @return ?ListNode
     */
    function getIntersectionNode($headA, $headB) {
        $node1 = $headA;
        $node2 = $headB;

        while ($node1 !== $node2) {
            if ($node1 == null) {
                $node1 = $headB;
            } else {
                $node1 = $node1->next;
            }

            if ($node2 == null) {
                $node2 = $headA;
            } else {
                $node2 = $node2->next;
            }
        }

        return $node1;
    }
}