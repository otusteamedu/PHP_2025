<?php

/**
 *  Сложность O(m + n)
 */
class Solution {

    /**
     * @param ListNode $headA
     * @param ListNode $headB
     * @return ListNode
     */
    function getIntersectionNode($headA, $headB) {
        if ($headA === null || $headB === null) {
            return null;
        }

        $currentA = $headA;
        $currentB = $headB;

        while ($currentA !== $currentB) {
            $currentA = ($currentA === null) ? $headB : $currentA->next;
            $currentB = ($currentB === null) ? $headA : $currentB->next;
        }

        return $currentA;
    }
}
