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
    public static function MergeTwoLists(ListNode $list1, ListNode $list2): ListNode
    {
        $dummy = new ListNode();
        $tail = $dummy;

        while ($list1 !== null && $list2 !== null) {
            if ($list1->val <= $list2->val) {
                $tail->next = $list1;
                $list1 = $list1->next;
            } else {
                $tail->next = $list2;
                $list2 = $list2->next;
            }
            $tail = $tail->next;
        }

        $tail->next = $list1 ?? $list2;

        return $dummy->next;
    }

    public static function createList(array $values): ?ListNode {
        if (empty($values)) {
            return null;
        }

        $head = new ListNode($values[0]);
        $current = $head;

        for ($i = 1; $i < count($values); $i++) {
            $current->next = new ListNode($values[$i]);
            $current = $current->next;
        }

        return $head;
    }

    public static function printLinkedList(?ListNode $head): void
    {
        $current = $head;
        while ($current !== null) {
            echo $current->val . " -> ";
            $current = $current->next;
        }
        echo "null\n";
    }
}

$list1 = Solution::createList([1, 6, 8, 10, 22]);
Solution::printLinkedList($list1);
$list2 = Solution::createList([-6, 2, 3, 4, 5, 9, 11, 27]);
Solution::printLinkedList($list2);
$list = Solution::mergeTwoLists($list1, $list2);
Solution::printLinkedList($list);