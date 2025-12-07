<?php

namespace Alisaselezneva\Code\Domain\Services;

class Solution {

    /**
     * @param ListNode $list1
     * @param ListNode $list2
     * @return ?ListNode
     */
    function mergeTwoLists($list1, $list2) : ?ListNode {
        $res = new ListNode();
        $tail = $res;

        while ($list1 !== null && $list2 !== null) {
            if ($list1->val < $list2->val) {
                $tail->next = new ListNode($list1->val, null);
                $list1 = $list1->next;
            }
            else {
                $tail->next = new ListNode($list2->val, null);
                $list2 = $list2->next;
            }
            $tail = $tail->next;
        }
        $tail->next = $list1 ?? $list2;

        return $res->next;
    }
}