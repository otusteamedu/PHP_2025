<?php

declare(strict_types=1);

/**
 * Definition for a singly-linked list.
 */
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

class Solution
{
    /**
     * @param ListNode $list1
     * @param ListNode $list2
     * @return ListNode
     */
    function mergeTwoLists($list1, $list2)
    {
        $result = new ListNode();
        $current = $result;

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

        $current->next = $list1 ?? $list2;

        return $result->next;
    }
}

// Cоздание связанного списка из массива
function arrayToList(array $arr): ?ListNode
{
    if ($arr === []) {
        return null;
    }

    $mainList = new ListNode($arr[0]);
    $currentList = $mainList;

    for ($i = 1; $i < count($arr); $i++) {
        $currentList->next = new ListNode($arr[$i]);
        $currentList = $currentList->next;
    }

    return $mainList;
}


// Преобразование связанного списка в массив
function listToArray(?ListNode $list): array
{
    $result = [];
    while ($list !== null) {
        $result[] = $list->val;
        $list = $list->next;
    }

    return $result;
}

// Для теста
function runTest(array $arr1, array $arr2, array $expected): void
{
    $nodeList1 = arrayToList($arr1);
    $nodeList2 = arrayToList($arr2);

    $solution = new Solution();
    $mergedList = $solution->mergeTwoLists($nodeList1, $nodeList2);

    $result = listToArray($mergedList);

    $status = $result === $expected ? 'PASSED' : 'FAILED';
    echo $status . PHP_EOL;
    echo '  Input:    list1 = ' . json_encode($arr1) . ', list2 = ' . json_encode($arr2) . PHP_EOL;
    echo '  Expected: ' . json_encode($expected) . PHP_EOL;
    echo '  Got:      ' . json_encode($result) . PHP_EOL . PHP_EOL;
}

echo '=== Тест mergeTwoLists ===' . PHP_EOL . PHP_EOL;

// Примеры из задачи
// Пример 1
// list1 = [1,2,4]
// list2 = [1,3,4]
// expected => [1,1,2,3,4,4]
runTest([1, 2, 4], [1, 3, 4], [1, 1, 2, 3, 4, 4]);

// Пример 2: 
// list1 = []
// list2 = []
// expected => []
runTest([], [], []);

// Пример 3: 
// list1 = []
// list2 = [0]
// expected => [0]
runTest([], [0], [0]);

// Дополнительные тесты
runTest([1], [2], [1, 2]);
runTest([5], [1, 2, 4], [1, 2, 4, 5]);
runTest([-10, -5, 0], [-7, -3, 1], [-10, -7, -5, -3, 0, 1]);
