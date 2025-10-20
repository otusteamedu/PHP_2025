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
    function getIntersectionNode($headA, $headB) {
        if ($headA === null || $headB === null) {
            return null;
        }
        
        $pointerA = $headA;
        $pointerB = $headB;
        
        while ($pointerA !== $pointerB) {
            $pointerA = ($pointerA === null) ? $headB : $pointerA->next;
            $pointerB = ($pointerB === null) ? $headA : $pointerB->next;
        }
        
        return $pointerA;
    }
}
?>