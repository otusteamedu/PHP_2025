<?php

//   Definition for a singly-linked list.
class ListNode
{
    public $val = 0;
    public $next = null;
    function __construct($val = 0, $next = null)
    {
        $this->val = $val;
        $this->next = $next;
    }
}

class Solution
{

    /**
     * @param ListNode $list1
     * @param ListNode $list2
     * @return ListNode
     */
    function mergeTwoLists($list1, $list2)
    {
        if(!isset($list1) && !isset($list2))
            return [];

        $end = new ListNode();
        $start = $end;

        while ($list1 || $list2) {
            if ($list1 && $list2) {
                if ($list1->val > $list2->val) {
                    $end->val = $list2->val;
                    $list2 = $list2->next;
                } else {
                    $end->val = $list1->val;
                    $list1 = $list1->next;
                }
            } else {
                if ($list1) {
                    $end->val = $list1->val;
                    $list1 = $list1->next;
                }
                if ($list2) {
                    $end->val = $list2->val;
                    $list2 = $list2->next;
                }
            }

            if ($list1!==null || $list2!==null) {
                $end->next = new ListNode();
                $end = $end->next;
            }
        }

        return $start;
    }
}