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
        
        $ptrA = $headA;
        $ptrB = $headB;
        
        while ($ptrA !== $ptrB) {
            $ptrA = ($ptrA === null) ? $headB : $ptrA->next;
            $ptrB = ($ptrB === null) ? $headA : $ptrB->next;
        }
        
        return $ptrA;
    }
}
