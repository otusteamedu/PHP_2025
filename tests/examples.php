<?php

require_once __DIR__ . '/../vendor/autoload.php';

use LeetCode\ListNode;
use LeetCode\IntersectionOfTwoLinkedLists;
use LeetCode\FractionToDecimal;

// --- Тесты для Intersection of Two Linked Lists ---
function testIntersection() {
    echo "Testing Intersection of Two Linked Lists:\n";
    $solver = new IntersectionOfTwoLinkedLists();

    // Case 1: Пересекаются
    $common = new ListNode(8, new ListNode(4, new ListNode(5)));
    $headA = new ListNode(4, new ListNode(1, $common));
    $headB = new ListNode(5, new ListNode(6, new ListNode(1, $common)));
    
    $result = $solver->getIntersectionNode($headA, $headB);
    echo "Case 1 (Intersecting): " . ($result === $common ? "PASSED" : "FAILED") . " (Val: " . ($result ? $result->val : 'null') . ")\n";

    // Case 2: Не пересекаются
    $headA2 = new ListNode(2, new ListNode(6, new ListNode(4)));
    $headB2 = new ListNode(1, new ListNode(5));
    $result2 = $solver->getIntersectionNode($headA2, $headB2);
    echo "Case 2 (No intersection): " . ($result2 === null ? "PASSED" : "FAILED") . "\n";
    echo "\n";
}

// --- Тесты для Fraction to Recurring Decimal ---
function testFraction() {
    echo "Testing Fraction to Recurring Decimal:\n";
    $solver = new FractionToDecimal();

    $cases = [
        [1, 2, "0.5"],
        [2, 1, "2"],
        [4, 333, "0.(012)"],
        [1, 6, "0.1(6)"],
        [-50, 8, "-6.25"],
        [0, 3, "0"]
    ];

    foreach ($cases as $case) {
        [$num, $den, $expected] = $case;
        $result = $solver->fractionToDecimal($num, $den);
        echo "Case $num/$den: " . ($result === $expected ? "PASSED" : "FAILED") . " (Result: $result)\n";
    }
}

testIntersection();
testFraction();
