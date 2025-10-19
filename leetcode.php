<?php

/**
 * Definition for a singly-linked list.
 * class ListNode {
 *     public $val = 0;
 *     public $next = null;
 *     function __construct($val = 0, $next = null) {
 *         $this->val = $val;
 *         $this->next = $next;
 *     }
 * }
 */
class Solution
{
    /**
     * @param  ListNode  $list1
     * @param  ListNode  $list2
     * @return ListNode
     */
    public function mergeTwoLists($list1, $list2)
    {
        $start = new ListNode;
        $end = $start;

        while (! is_null($list1) && ! is_null($list2)) {
            if ($list1->val <= $list2->val) {
                $end->next = $list1;
                $list1 = $list1->next;
            } else {
                $end->next = $list2;
                $list2 = $list2->next;
            }
            $end = $end->next;
        }
        $end->next = $list1 ?? $list2;

        return $start->next;
    }
}
/**
* Сложность алгоритма O(n + m), где n - длина первого списка, m - длина второго, так как
* цикл проходит по обоим спискам только один раз
*/
