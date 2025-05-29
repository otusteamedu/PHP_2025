<?php


// Создание тестовых данных
// Список A: 4 -> 1 -> 8 -> 4 -> 5
// Список B: 5 -> 6 -> 1 -> 8 -> 4 -> 5
// Пересечение в узле со значением 8

$commonNode = new ListNode(8, new ListNode(4, new ListNode(5)));
$headA = new ListNode(4, new ListNode(1, $commonNode));
$headB = new ListNode(5, new ListNode(6, new ListNode(1, $commonNode)));

$solution = new Solution();
$result = $solution->getIntersectionNode($headA, $headB);

// Вывод результата
if ($result !== null) {
echo "Intersected at '{$result->val}'\n";
} else {
echo "No intersection\n";
}