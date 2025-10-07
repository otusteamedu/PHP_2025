<?php

declare(strict_types=1);

/**
 * Definition for singly-linked list.
 */
class ListNode
{
    public int $val;
    public ?ListNode $next;

    public function __construct(int $val = 0, ?ListNode $next = null)
    {
        $this->val = $val;
        $this->next = $next;
    }
}

class Solution160
{
    /**
     * Возвращает первый общий node для двух списков
     * @param ListNode|null $headA
     * @param ListNode|null $headB
     * @return ListNode|null
     */
    public static function getIntersectionNode(?ListNode $headA, ?ListNode $headB): ?ListNode
    {
        if ($headA === null || $headB === null) {
            return null;
        }

        $pA = $headA;
        $pB = $headB;

        while ($pA !== $pB) {
            $pA = ($pA === null) ? $headB : $pA->next;
            $pB = ($pB === null) ? $headA : $pB->next;
        }

        return $pA;
    }
}
