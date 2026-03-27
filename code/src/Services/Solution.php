<?php
declare(strict_types=1);

namespace Ak\Hw\Services;

class Solution
{
    /**
     * @param ?ListNode $headA
     * @param ?ListNode $headB
     * @return ?ListNode
     *
     *  Сложность алгоритма: O(m + n), где m и n - количество элементов в списках A и B.
     *
     */
    function getIntersectionNode(?ListNode $headA, ?ListNode $headB): ?ListNode
    {
        if ($headA === null || $headB === null) {
            return null;
        }

        $listA = $headA;
        $listB = $headB;

        // Базовый случай совпадение $listA и $listB
        while ($listA !== $listB) {
            $listA = ($listA === null) ? $headB : $listA->next;
            $listB = ($listB === null) ? $headA : $listB->next;
        }

        return $listA;
    }
}
