<?php
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
        $current1 = $list1;
        $current2 = $list2;
        $result = null;

        while ($current1 || $current2) {
            if (!$current2 || ($current1 && $current1->val < $current2->val)) {
                $val = $current1->val;
                $current1 = $current1->next;
            } else {
                $val = $current2->val;
                $current2 = $current2->next;
            }

            $newNode = new ListNode($val);
            if (!$result) {
                $result = $newNode;
            } else {
                $oldNode->next = $newNode;
            }
            $oldNode = $newNode;
        }

        return $result;
    }
}