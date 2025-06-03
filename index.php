<?php

declare(strict_types=1);

/**
 * Definition for a singly-linked list.
 * class ListNode {
 *     public $val = 0;
 *     public $next = null;
 *     function __construct($val = 0, $next = null) {
 *         $this->val = $val;
 *         $this->next = $next;
 *     }
 * }
 */
class Solution {

    /**
     * @param ListNode $list1
     * @param ListNode $list2
     * @return ListNode
     */
    function mergeTwoLists($list1, $list2) {
        
        $result = $current = new ListNode(-101);

        $head1 = $list1;
        $head2 = $list2;

        while ($head1 !== null || $head2 !== null) {

            if (($head1 == null && $head2 !== null) || ($head2 !== null && $head1->val > $head2->val)) {
                $current->next = $head2;
                $head2 = $head2->next;
            } else if (($head1 !== null && $head2 == null) || ($head1 !== null &&  $head1->val <= $head2->val)) {
                $current->next = $head1;
                $head1 = $head1->next;
            }

            $current = $current->next;
        }

        return $result->next;
    }
}
