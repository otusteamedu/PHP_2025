<?php

namespace Alisaselezneva\Code\Domain\Services;

use SplObjectStorage;

class Solution {
    
    /**
     * @param ListNode $headA
     * @param ListNode $headB
     * @return ?ListNode
     */
    function getIntersectionNode($headA, $headB) {
        $hashA = new SplObjectStorage();
        while($headA != null) {
            $hashA[$headA] = true;
            $headA = $headA->next;
        }

        while($headB != null) {
            if (isset($hashA[$headB])) {
                
                return $headB;
            }
            $headB = $headB->next;
        }

        return null;
    }
}