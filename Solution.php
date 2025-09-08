<?php
/*
**
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
        //Сохраняем начало списка в отдельной переменной
        $startNode = $resultNode = new ListNode();

        while ($list1 !== null && $list2 !== null) {
            if ($list1->val < $list2->val) {
                $resultNode->next = $list1;
                $list1 = $list1->next;
            } else {
                $resultNode->next = $list2;
                $list2 = $list2->next;
            }
            $resultNode = $resultNode->next;
        }
        // //Добавляем в конец результата остаток от более длинного списка
        if ($list1 !== null) {
            $resultNode->next = $list1;
        } else {
            $resultNode->next = $list2;
        }

        //Возвращаем второй элемент, т.к. первый пустой
        return $startNode->next;
    }
}