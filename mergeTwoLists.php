<?php

class ListNode
{
    public $val = 0;
    public $next = null;

    function __construct($val = 0, $next = null)
    {
        $this->val = $val;
        $this->next = $next;
    }
}

// Сложность O(n), потому что на каждой итерации мы переходим к следующему узлу одного из списков
function mergeTwoLists(?ListNode $list1, ?ListNode $list2): ?ListNode
{
    $dummy = new ListNode();
    $current = $dummy;

    while ($list1 !== null && $list2 !== null) {
        if ($list1->val < $list2->val) {
            $current->next = $list1;
            $list1 = $list1->next;
        } else {
            $current->next = $list2;
            $list2 = $list2->next;
        }
        $current = $current->next;
    }

    $current->next = $list1 ?? $list2;

    return $dummy->next;
}

// Функция для создания связанного списка из массива
function createLinkedList(array $values): ?ListNode
{
    if (empty($values)) {
        return null;
    }

    $head = new ListNode(array_shift($values));
    $current = $head;

    foreach ($values as $value) {
        $current->next = new ListNode($value);
        $current = $current->next;
    }

    return $head;
}

// Функция для вывода связанного списка
function printLinkedList(?ListNode $head): void
{
    $values = [];
    while ($head !== null) {
        $values[] = $head->val;
        $head = $head->next;
    }
    echo implode(" -> ", $values) . PHP_EOL;
}

// Пример использования
$list1 = createLinkedList([1, 1, 3, 5]);
$list2 = createLinkedList([2, 4, 6]);

echo "Список 1: ";
printLinkedList($list1);

echo "Список 2: ";
printLinkedList($list2);

$mergedList = mergeTwoLists($list1, $list2);

echo "Объединённый список: ";
printLinkedList($mergedList);
