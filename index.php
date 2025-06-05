<?php
declare(strict_types=1);

/**
 * Определение для односвязного списка.
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
        // Создаем фиктивный узел, чтобы упростить логику
        $dummy = new ListNode();
        $current = $dummy;

        // Пока оба списка не пусты
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

        // Если остались узлы в list1 или list2
        if ($list1 !== null) {
            $current->next = $list1;
        } else {
            $current->next = $list2;
        }

        // Возвращаем объединенный список, пропуская фиктивный узел
        return $dummy->next;
    }
}