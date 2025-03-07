<?php

namespace classes;

class Solution {
    /**
     * Merge two sorted linked lists into one sorted list.
     *
     * @param ListNode|null $list1
     * @param ListNode|null $list2
     * @return ListNode|null
     */
    public function mergeTwoLists($list1, $list2)
    {
        $mergedList = new ListNode();
        $current = $mergedList;

        while ($list1 !== null && $list2 !== null) {
            if ($list1->val <= $list2->val) {
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
        } elseif ($list2 !== null) {
            $current->next = $list2;
        }

        return $mergedList->next;
    }

    public function makeLinkedListFromArray(array $array):ListNode
    {
        $linkedList = new ListNode($array[0]);

        for ($i = 1; $i < count($array); $i++) {
            $linkedList->appendToListEnd($array[$i]);
        }

        return $linkedList;
    }
}