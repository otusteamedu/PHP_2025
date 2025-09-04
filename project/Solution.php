<?php

/* Определение класса ListNode
class ListNode {
    public $val = 0;
    public $next = null;
    function __construct($val = 0, $next = null) {
        $this->val = $val;
        $this->next = $next;
    }
}*/

// Класс решения
class Solution {
    /**
     * @param ListNode|null $list1
     * @param ListNode|null $list2
     * @return ListNode|null
     */
    function mergeTwoLists($list1, $list2) {
        $dummy = new ListNode(0);
        $current = $dummy;

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
        }
        if ($list2 !== null) {
            $current->next = $list2;
        }

        return $dummy->next;
    }
}

// Вспомогательная функция для создания списка из массива
function createList($arr) {
    if (empty($arr)) {
        return null;
    }

    $dummy = new ListNode(0);
    $current = $dummy;

    foreach ($arr as $val) {
        $current->next = new ListNode($val);
        $current = $current->next;
    }

    return $dummy->next;
}

// Вспомогательная функция для преобразования списка в массив
function listToArray($list) {
    $result = [];
    while ($list !== null) {
        $result[] = $list->val;
        $list = $list->next;
    }
    return $result;
}

// Тестирование
$solution = new Solution();

// Пример 1: list1 = [1,2,4], list2 = [1,3,4]
$list1 = createList([1, 2, 4]);
$list2 = createList([1, 3, 4]);
$result = $solution->mergeTwoLists($list1, $list2);
echo "Пример 1: " . json_encode(listToArray($result)) . "\n"; // Ожидаемый вывод: [1,1,2,3,4,4]

// Пример 2: list1 = [], list2 = []
$list1 = createList([]);
$list2 = createList([]);
$result = $solution->mergeTwoLists($list1, $list2);
echo "Пример 2: " . json_encode(listToArray($result)) . "\n"; // Ожидаемый вывод: []

// Пример 3: list1 = [], list2 = [0]
$list1 = createList([]);
$list2 = createList([0]);
$result = $solution->mergeTwoLists($list1, $list2);
echo "Пример 3: " . json_encode(listToArray($result)) . "\n"; // Ожидаемый вывод: [0]

?>