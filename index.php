<?php

class ListNode {
    public $val = 0;
    public ?ListNode $next = null;
    function __construct($val = 0, $next = null) {
        $this->val = $val;
        $this->next = $next;
    }
}

class FillListNode {
    public function generateListNode(array $nodes): ?ListNode
    {
        $newList = new ListNode();
        $indicator = $newList;
        foreach ($nodes as $node) {
            $indicator->next = new ListNode($node);
            $indicator = &$indicator->next;
        }
        return $newList->next;
    }
}

function mergeTwoLists(ListNode $list1, ListNode $list2) {
    $newList = new ListNode();
    $indicator = $newList;
    while ($list1 !== null || $list2 !== null) {
        if (empty($list1) && $list2) {
            $indicator->next = new ListNode($list2->val);
            $indicator = &$indicator->next;
            $list2 = $list2->next;
            continue;
        }

        if ($list1 && empty($list2)) {
            $indicator->next = new ListNode($list1->val);
            $indicator = &$indicator->next;
            $list1 = $list1->next;
            continue;
        }
        if ($list1->val === $list2->val) {
            $indicator->next = new ListNode($list1->val, new ListNode($list2->val));
            $indicator = &$indicator->next->next;
            $list1 = $list1->next;
            $list2 = $list2->next;
            continue;
        }

        if ($list1->val < $list2->val) {
            $indicator->next = new ListNode($list1->val);
            $indicator = &$indicator->next;
            $list1 = $list1->next;
            continue;
        }

        if ($list1->val > $list2->val) {
            $indicator->next = new ListNode($list2->val);
            $indicator = &$indicator->next;
            $list2 = $list2->next;
        }

    }
    return $newList->next;
}

$fillList = new FillListNode();

$list1 = [1, 2, 4, 6, 7, 8, 10];
$list2 = [ 7, 8, 10, 12, 45];
// в худшем случае алгоритм пройдет по всем нодам первого списка и второго
// поэтому сложность О(n+m)
$list1 = $fillList->generateListNode($list1);
$list2 = $fillList->generateListNode($list2);
mergeTwoLists($list1, $list2);