<?php
// Решение задачи: Intersection of Two Linked Lists
// Сложность: O(m + n)

class ListNode
{
    public $val = 0;
    public $next = null;

    public function __construct($val = 0)
    {
        $this->val = $val;
    }
}

function getIntersectionNode(?ListNode $headA, ?ListNode $headB): ?ListNode
{
    if ($headA === null || $headB === null) {
        return null;
    }

    $pointerA = $headA;
    $pointerB = $headB;

    while ($pointerA !== $pointerB) {
        $pointerA = ($pointerA === null) ? $headB : $pointerA->next;
        $pointerB = ($pointerB === null) ? $headA : $pointerB->next;
    }

    return $pointerA;
}

echo "--- Тест1:\n";
$common = new ListNode(8);
$common->next = new ListNode(4);
$common->next->next = new ListNode(5);

$headA = new ListNode(4);
$headA->next = new ListNode(1);
$headA->next->next = $common;

$headB = new ListNode(5);
$headB->next = new ListNode(6);
$headB->next->next = new ListNode(1);
$headB->next->next->next = $common;

$result = getIntersectionNode($headA, $headB);
echo 'Результат: ' . print_r($result, true) . "\n";

echo "--- Тест2:\n";
$common = new ListNode(2);
$common->next = new ListNode(4);

$headA = new ListNode(1);
$headA->next = new ListNode(9);
$headA->next->next = new ListNode(1);
$headA->next->next->next = $common;

$headB = new ListNode(3);
$headB->next = $common;

$result = getIntersectionNode($headA, $headB);
echo 'Результат: ' . print_r($result, true) . "\n";

echo "--- Тест3:\n";
$headA = new ListNode(2);
$headA->next = new ListNode(6);
$headA->next->next = new ListNode(4);

$headB = new ListNode(1);
$headB->next = new ListNode(5);

$result = getIntersectionNode($headA, $headB);
echo 'Результат: ' . print_r($result, true) . "\n";
