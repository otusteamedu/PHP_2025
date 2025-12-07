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
        if (is_null($list1)) return $list2;
        if (is_null($list2)) return $list1;

        return $list1;
    }
}