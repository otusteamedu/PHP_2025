<?php

declare(strict_types=1);

/**
 * @see: https://leetcode.com/problems/intersection-of-two-linked-lists/description
 *
 * @note:
 * Временная сложность:
 * Каждый указатель проходит максимум m + n узлов → O(m + n)
 *
 * Пространственная сложность:
 * Только два указателя → O(1)
 *
 */
class IntersectionOfTwoLinkedLists
{
    public function getIntersectionNode(ListNode $headA, ListNode $headB): ?ListNode
    {
        $pA = $headA;
        $pB = $headB;

        while ($pA !== $pB) {
            // Если дошли до конца списка A - переключаемся на начало B
            $pA = ($pA === null) ? $headB : $pA->next;
            // Если дошли до конца списка B - переключаемся на начало A
            $pB = ($pB === null) ? $headA : $pB->next;
        }

        // Либо это узел пересечения, либо null (если пересечения нет)
        return $pA;
    }
}
