<?php

declare(strict_types=1);

class ListNode
{
    public int $val = 0;
    public ListNode|null $next = null;

    function __construct($val)
    {
        $this->val = $val;
    }
}

class Solution {
    private $passedBoth = false;

    function getIntersectionNode(?ListNode $headA, ?ListNode $headB): ?ListNode {
        return $this->findNode($headA, $headB, $headA, $headB);
    }

    private function findNode(
        ?ListNode $a,
        ?ListNode $b,
        ?ListNode $origA,
        ?ListNode $origB
    ): ?ListNode {
        // Базовый случай - найдено пересечение
        if ($a === $b) {
            return $a;
        }

        // Оба указателя null после полного прохода
        if ($a === null && $b === null && $this->passedBoth) {
            return null;
        }

        // Отметка о полном проходе
        if ($a === null && $b === null) {
            $this->passedBoth = true;
        }

        // Переключение при достижении конца
        $nextA = ($a === null) ? $origB : $a->next;
        $nextB = ($b === null) ? $origA : $b->next;

        return $this->findNode($nextA, $nextB, $origA, $origB);
    }
}
