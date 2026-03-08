<?php
declare(strict_types=1);

namespace Ak\Hw\Services;

/**
 * Definition for a singly-linked list.
 */

class Solution
{
    /**
     * @param ?ListNode $headA
     * @param ?ListNode $headB
     * @return ?ListNode
     */
    public function getIntersectionNode(?ListNode $headA, ?ListNode $headB): ?ListNode
    {
        if ($headA === null) {
            return null;
        }

        $intersect = $headB;
        while ($intersect !== null) {
            if ($headA === $intersect) {
                return $headA;
            }
            $intersect = $intersect->next;
        }

        return $this->getIntersectionNode($headA->next, $headB);
    }
}