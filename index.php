<?php

require __DIR__ . '/vendor/autoload.php';

use app\ListNode;
use app\Solution;

// Вспомогательная функция для создания связанного списка из массива
function createList($list)
{
    // Сортируем первичный список
    $n = count($list);
    for ($i = 0; $i < $n - 1; $i++) {
        for ($j = 0; $j < $n - $i - 1; $j++) {
            if ($list[$j] > $list[$j + 1]) {
                $temp = $list[$j];
                $list[$j] = $list[$j + 1];
                $list[$j + 1] = $temp;
            }
        }
    }

    $dummy = new ListNode();
    $current = $dummy;
    foreach ($list as $val) {
        $current->next = new ListNode($val);
        $current = $current->next;
    }
    return $dummy->next; // Возвращаем начало списка (или null, если список пуст)
}

// Вспомогательная функция для вывода связанного списка
function printList($list)
{
    while ($list !== null) {
        echo $list->val . '->';
        $list = $list->next;
    }
    echo "null\n";
}

$solution = new Solution();

// Пример 1
$list1 = createList([2, 4, 1, 51, 3, 45, 17]);
$list2 = createList([1, 3, 4, 11, 3, 6]);
$mergedList = $solution->mergeTwoLists($list1, $list2);
printList($mergedList); // Вывод: 1->1->2->3->3->3->4->4->6->11->17->45->51->null

// Пример 2
$list3 = createList([]);
$list4 = createList([]);
$mergedList2 = $solution->mergeTwoLists($list3, $list4);
printList($mergedList2); // Вывод: null

// Пример 3
$list5 = createList([]);
$list6 = createList([0]);
$mergedList3 = $solution->mergeTwoLists($list5, $list6);
printList($mergedList3); // Вывод: 0 -> null
