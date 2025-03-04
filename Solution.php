<?php
declare(strict_types=1);

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

class Solution {

    /**
     * @param ListNode $list1
     * @param ListNode $list2
     * @return ListNode
     */
    public function mergeTwoLists(?ListNode $list1, ?ListNode $list2): ?ListNode
    {
        $head = $tail = new ListNode();
        
        while (isset($list1, $list2)) {

            if ($list1->val < $list2->val) {
                $tail->next = $list1;
                $list1 = $list1->next;
            } else {
                $tail->next = $list2;
                $list2 = $list2->next;
            }

            $tail = $tail->next;
        }

        $tail->next = $list1 ?? $list2;

        return $head->next;
    }
}

$solution = new Solution();

// список1 = [1,2,4], список2 = [1,3,4]
$list1_1 = new ListNode(4);
$list1_2 = new ListNode(2, $list1_1);
$list1 = new ListNode(2, $list1_2);

$list2_1 = new ListNode(4);
$list2_2 = new ListNode(3, $list2_1);
$list2 = new ListNode(1, $list2_2);

print_r($solution->mergeTwoLists($list1, $list2));

// список1 = [], список2 = []
$list1 = new ListNode();
$list2 = new ListNode();

print_r($solution->mergeTwoLists($list1, $list2));

// список1 = [], список2 = [0]
$list1 = new ListNode();

$list2_1 = new ListNode();
$list2 = new ListNode(0, $list2_1);

print_r($solution->mergeTwoLists($list1, $list2));
?>
