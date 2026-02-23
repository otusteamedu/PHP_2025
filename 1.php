<?php

class ListNode
{
    public $val = 0;
    public $next = null;

    function __construct($val = 0)
    {
        $this->val = $val;
    }
}

class Solution
{
    /**
     * @param ListNode $headA
     * @param ListNode $headB
     * @return ListNode
     */
    function getIntersectionNode($headA, $headB)
    {
        $lenA = 0;
        $lenB = 0;
        $pointerA = $headA;
        $pointerB = $headB;

        for ($node = $headA; $node !== null; $node = $node->next) {
            $lenA++;
        }

        for ($node = $headB; $node !== null; $node = $node->next) {
            $lenB++;
        }

        $diff = $lenA - $lenB;

        if ($diff > 0) {
            for ($i = 0; $i < $diff; $i++) {
                $pointerA = $pointerA->next;
            }
        } else {
            for ($i = 0; $i < abs($diff); $i++) {
                $pointerB = $pointerB->next;
            }
        }

        while ($pointerA !== $pointerB) {
            $pointerA = $pointerA->next;
            $pointerB = $pointerB->next;
        }

        return $pointerA;
    }
}

// Строит два списка и объединяет их в узле с нужным значением
function buildLists(array $arr1, array $arr2, ?int $intersectionValue): array
{
    $makeList = function (array $values): ?ListNode {
        if ($values === []) {
            return null;
        }

        $head = new ListNode($values[0]);
        $current = $head;

        for ($i = 1, $count = count($values); $i < $count; $i++) {
            $current->next = new ListNode($values[$i]);
            $current = $current->next;
        }

        return $head;
    };

    $headA = $makeList($arr1);
    $headB = $makeList($arr2);

    if ($intersectionValue === null || $headA === null || $headB === null) {
        return [$headA, $headB];
    }

    $intersectionNode = null;
    for ($node = $headA; $node !== null; $node = $node->next) {
        if ($node->val === $intersectionValue) {
            $intersectionNode = $node;
            break;
        }
    }

    if ($intersectionNode === null) {
        return [$headA, $headB];
    }

    $prev = null;
    for ($node = $headB; $node !== null; $node = $node->next) {
        if ($node->val === $intersectionValue) {
            if ($prev === null) {
                $headB = $intersectionNode;
            } else {
                $prev->next = $intersectionNode;
            }
            break;
        }
        $prev = $node;
    }

    return [$headA, $headB];
}

// Для теста
function runTest(array $arr1, array $arr2, ?int $expected): void
{
    [$nodeList1, $nodeList2] = buildLists($arr1, $arr2, $expected);

    $solution = new Solution();
    $result = $solution->getIntersectionNode($nodeList1, $nodeList2);

    $resultVal = $result === null ? null : $result->val;
    $status = $resultVal === $expected ? 'PASSED' : 'FAILED';
    echo $status . PHP_EOL;
    echo '  Input:    list1 = ' . json_encode($arr1) . ', list2 = ' . json_encode($arr2) . PHP_EOL;
    echo '  Expected: ' . json_encode($expected) . PHP_EOL;
    echo '  Got:      ' . json_encode($resultVal) . PHP_EOL . PHP_EOL;
}

echo '=== getIntersectionNode ===' . PHP_EOL . PHP_EOL;

// Примеры из задачи
// Пример 1
$listA = [4, 1, 8, 4, 5];
$listB = [5, 6, 1, 8, 4, 5];
$expected = 8;
runTest($listA, $listB, $expected);

// Пример 2: 
$listA = [1, 9, 1, 2, 4];
$listB = [3, 2, 4];
$expected = 2;
runTest($listA, $listB, $expected);

// Пример 3: 
$listA = [2, 6, 4];
$listB = [1, 5];
$expected = null;
runTest($listA, $listB, $expected);
