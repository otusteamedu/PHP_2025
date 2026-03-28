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
    function mergeTwoLists(?ListNode $list1, ?ListNode $list2): ?ListNode {
        $result = $currentNode = new ListNode();

        if ($list1 === null) {
            return $list2;
        }

        if ($list2 === null) {
            return $list1;
        }

        while ($list1 !== null || $list2 !== null) {
            if ($list2 === null) {
                $currentNode->next = new ListNode($list1->val);
                $currentNode = $currentNode->next;
                $list1 = $list1->next;

                continue;
            }

            if ($list1 === null) {
                $currentNode->next = new ListNode($list2->val);
                $currentNode = $currentNode->next;
                $list2 = $list2->next;

                continue;
            }

            if ($list1->val === $list2->val) {
                $currentNode->next = new ListNode($list1->val);
                $currentNode = $currentNode->next;
                $list1 = $list1->next;

                $currentNode->next = new ListNode($list2->val);
                $currentNode = $currentNode->next;
                $list2 = $list2->next;

                continue;
            }

            if ($list1->val < $list2->val) {
                $currentNode->next = new ListNode($list1->val);
                $list1 = $list1->next;
            } else {
                $currentNode->next = new ListNode($list2->val);
                $list2 = $list2->next;
            }

            $currentNode = $currentNode->next;
        }

        return $result->next;
    }
}
