<?php

/**
 * Definition for a singly-linked list.
 * class ListNode {
 *     public $val = 0;
 *     public $next = null;
 *     function __construct($val) { $this->val = $val; }
 * }
 */

class Solution {
    /**
     * @param ListNode $headA
     * @param ListNode $headB
     * @return ListNode
     */
    function getIntersectionNode(?ListNode $headA, ?ListNode $headB): ?ListNode {
        if ($headA === null || $headB === null) {
            return null;
        }

        $a = $headA;
        $countA = 0;

        $b = $headB;
        $countB = 0;

        while (true) {
            if ($a === null && $b === null) {
                break;
            }

            if ($a !== null) {
                $countA++;
                $a = $a->next;
            }

            if ($b !== null) {
                $countB++;
                $b = $b->next;
            }
        }

        $a = $headA;
        $b = $headB;

        while (true) {
            if ($countA !== $countB) {
                if ($countA > $countB) {
                    $a = $a->next;
                    $countA--;

                    continue;
                }

                if ($countA < $countB) {
                    $b = $b->next;
                    $countB--;

                    continue;
                }
            }

            if ($a === $b) {
                break;
            }

            $a = $a->next;
            $b = $b->next;
        }

        return $a;
    }
} //сложность o(n+m), один раз проходим по ноде с самым большим кол-вом элементов, затем в худшем случае полностью пройдемся по $headA и $headB