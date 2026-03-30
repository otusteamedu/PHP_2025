<?php
class Solution {
    /**
     * @param ListNode $headA
     * @param ListNode $headB
     * @return ListNode
     * 
     * Сложность - O(m + n), где m и n длины списков
     */

    
    function getIntersectionNode($headA, $headB)
    {
        $hashTable = new SplObjectStorage();
        $list = $headA;
        while ($list) {
            $hashTable[$list] = $list;
            $list = $list->next;
        }
        $listB = $headB;
        while ($listB) {
            if (isset($hashTable[$listB]) && $hashTable[$listB] === $listB) {
                return $listB;
            }
            $listB = $listB->next;
        }

        return 0;
    }

}