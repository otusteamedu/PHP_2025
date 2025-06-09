<?php

class ListNode
{
    public $val;
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
     * @param ListNode|null $list1
     * @param ListNode|null $list2
     * @return ListNode|null
     */
    function mergeTwoLists(?ListNode $list1, ?ListNode $list2): ?ListNode
    {
        $new = new ListNode();
        $current = $new;

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

        $current->next = $list1 ?? $list2;

        return $new->next;
    }
}

function createLinkedList(array $values): ?ListNode {
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

function printLinkedList(?ListNode $node): array
{
    $arr = [];
    while ($node !== null) {
        $arr[] = $node->val;
        $node = $node->next;
    }
    return $arr;
}

$list1 = createLinkedList([1, 2, 4]);
$list2 = createLinkedList([1, 3, 4]);

$solution = new Solution();
$mergedList = $solution->mergeTwoLists($list1, $list2);

var_dump(printLinkedList($mergedList));

$list1 = createLinkedList([]);
$list2 = createLinkedList([]);
$mergedList = $solution->mergeTwoLists($list1, $list2);
var_dump(printLinkedList($mergedList));

$list1 = createLinkedList([]);
$list2 = createLinkedList([0]);
$mergedList = $solution->mergeTwoLists($list1, $list2);
var_dump(printLinkedList($mergedList));
