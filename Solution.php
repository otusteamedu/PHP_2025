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
class Solution {

    /**
     * @param ListNode $list1
     * @param ListNode $list2
     * @return ListNode
     */
    function mergeTwoLists($list1, $list2) {

        if($list1 === null && $list2 === null){
            return null;
        }
        if ($list1 == null) return $list2;
        if ($list2 == null) return $list1;
        if ($list1->val <= $list2->val) {
            $resultList = $list1;
            $list1 = $list1->next;
        } else {
            $resultList = $list2;
            $list2 = $list2->next;
        }
        $variableList = $resultList;
        while($list1 != null && $list2 != null){
            if ($list1->val <= $list2->val) {
                $variableList->next = $list1;
                $list1 = $list1->next;
            }else{
                $variableList->next = $list2;
                $list2 = $list2->next;
            }
            $variableList = $variableList->next;
        }
        if($list1 != null){
            $variableList->next = $list1;
        }else{
            $variableList->next = $list2;
        }
        return $resultList;
    }
}