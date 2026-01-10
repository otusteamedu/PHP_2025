<?php

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
        $head = new ListNode(0);
        $cur = $head;
        while ($list1 !== null && $list2 !== null) {
            if ($list1->val > $list2->val) {
                $cur->next = $list2;
                $list2 = $list2->next;
            } else {
                $cur->next = $list1;
                $list1 = $list1->next;
            }
            $cur = $cur->next;
        }

        $cur->next = $list1 ?: $list2;
        return $head->next;
    }
}

//$res = (new Solution())->mergeTwoLists(
//    new ListNode(1, new ListNode(3, new ListNode(4))),
//    new ListNode(1, new ListNode(2, new ListNode(4)))
//);
//
//while ($res) {
//    echo $res->val . ($res->next ? " ==> " : "");
//    $res = $res->next;
//}