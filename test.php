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

class ListNode {
    public $val = 0;
    public $next = null;
    function __construct($val = 0, $next = null) {
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

// Вспомогательная функция для создания списка из массива
function createList(array $arr) {
    if (empty($arr)) return null;
    $head = new ListNode($arr[0]);
    $current = $head;
    for ($i = 1; $i < count($arr); $i++) {
        $current->next = new ListNode($arr[$i]);
        $current = $current->next;
    }
    return $head;
}

// Вспомогательная функция для преобразования списка в массив
function listToArray($head) {
    $arr = [];
    while ($head !== null) {
        $arr[] = $head->val;
        $head = $head->next;
    }
    return $arr;
}

// Пример использования:
$solution = new Solution();

// Пример 1
$list1 = createList([1,2,4]);
$list2 = createList([1,3,4]);
$merged = $solution->mergeTwoLists($list1, $list2);
print_r(listToArray($merged)); // [1,1,2,3,4,4]

// Пример 2
$list1 = createList([]);
$list2 = createList([]);
$merged = $solution->mergeTwoLists($list1, $list2);
print_r(listToArray($merged)); // []

// Пример 3
$list1 = createList([]);
$list2 = createList([0]);
$merged = $solution->mergeTwoLists($list1, $list2);
print_r(listToArray($merged)); // [0]