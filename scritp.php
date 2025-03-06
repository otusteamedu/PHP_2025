<?php 

class ListNode {
    public $val;
    public $next;
    public function __construct($val = 0, $next = null)
    {
        $this->val = $val;
        $this->next = $next;
    }
}
 
 class Solution {
    /**
     * @param ListNode $list1
     * @param ListNode $list2
     * @return ListNode
     */

    function mergeTwoLists($list1, $list2) {
        $dummy = new ListNode(0);
        $current = $dummy;

        while ($list1 != null AND $list2 != null) {
            if ($list1->val <= $list2->val) {
                $current->next = $list1;
                $list1 = $list1->next;
            } else {
                $current->next = $list2;
                $list2 = $list2->next;
            }
            $current = $current->next;
        }
        if ($list1 != null) {
            $current->next = $list1;
        } elseif ($list2 != null) {
            $current->next = $list2;
        }
        return $dummy->next;
    }
}

$list1 = new ListNode(1, new ListNode(2, new ListNode(4)));
$list2 = new ListNode(1, new ListNode(3, new ListNode(4)));

$sol = new Solution();
$res = $sol->mergeTwoLists($list1, $list2);

// Функция для вывода значений списка
function printList($node) {
    while ($node != null) {
        echo $node->val . " ";
        $node = $node->next;
    }
}

// Вывод результата
printList($res);
