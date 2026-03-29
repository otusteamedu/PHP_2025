<?php

declare(strict_types=1);

class ListNode
{
    public mixed $val = 0;
    public mixed $next = null;

    function __construct($val = 0, $next = null)
    {
        $this->val = $val;
        $this->next = $next;
    }
}

class Solution
{
    /**
     * @param ListNode|null $list1
     * @param ListNode|null $list2
     * @return ListNode
     */
    static public function mergeTwoLists(?ListNode $list1, ?ListNode $list2): ListNode
    {
        if ($list1 === null) {
            return $list2;
        }

        if ($list2 === null) {
            return $list1;
        }

        $head = null;

        if ($list1->val < $list2->val) {
            $head = $list1;
            $list1 = $list1->next;
        } else {
            $head = $list2;
            $list2 = $list2->next;
        }

        $current = $head;

        while ($list1 !== null && $list2 !== null) {
            if ($list1->val < $list2->val) {
                $current->next = $list1;
                $list1 = $list1->next;
            } else {
                $current->next = $list2;
                $list2 = $list2->next;
            }

            $current = $current->next;
        }

        if ($list1 !== null) {
            $current->next = $list1;
        } else {
            $current->next = $list2;
        }

        return $head;
    }

    static public function printLinkedList(ListNode $head): void
    {
        $current = $head;
        while ($current !== null) {
            var_dump($current->val);
            $current = $current->next;
        }
    }
}

$list1 = new ListNode(1);
$list1->next = new ListNode(2);
$list1->next->next = new ListNode(4);

$list2 = new ListNode(1);
$list2->next = new ListNode(3);
$list2->next->next = new ListNode(4);

$mergedList = Solution::mergeTwoLists($list1, $list2);
Solution::printLinkedList($mergedList);
