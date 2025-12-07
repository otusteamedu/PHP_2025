<?php

declare(strict_types=1);

class ListNode {
    public $val = 0;
    public $next = null;
    function __construct($val = 0, $next = null) {
        $this->val = $val;
        $this->next = $next;
    }
}
class Solution {
    function mergeTwoLists(ListNode|null $list1, ListNode|null $list2): ListNode|null
    {
        if (!$list1) return $list2;
        if (!$list2) return $list1;

        if($list1->val > $list2->val) {
            return $this->getHeadMergedLists($list2, $list1);
        }

        return $this->getHeadMergedLists($list1, $list2);
    }

    function getHeadMergedLists(ListNode $mainListNode, ListNode $otherListNode): ListNode
    {
        $resultNode = $mainListNode;

        return $resultNode;
    }
}