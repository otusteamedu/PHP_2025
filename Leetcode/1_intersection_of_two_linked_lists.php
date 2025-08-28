<?php

declare(strict_types=1);

/**
 * @param ?ListNode $headA
 * @param ?ListNode $headB
 * @return ?ListNode
 *
 * Временная сложность: O(m + n) - проходим каждый список максимум дважды
 * Память: O(1) - используем только два указателя
 */
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
