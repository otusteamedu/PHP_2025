<?php

declare(strict_types=1);


class ListNode {
    public $val = 0;
    public $next = null;
    function __construct($val) {
        $this->val = $val;
    }
}

class Solution {
    /**
     * @param ListNode $headA
     * @param ListNode $headB
     * @return ListNode
     */
    public function getIntersectionNode(ListNode $headA, ListNode $headB): ?ListNode
    {

        $hash = [];
        $elementA = $headA;
        while (!is_null($elementA)) {
            $hash[$this->getHash($elementA)] = '';
            $elementA = $elementA->next;
        }

        $elementB = $headB;

        while (!is_null($elementB)) {
            $elementBHash = $this->getHash($elementB);

            if (isset($hash[$elementBHash])) {
                return $elementB;
            }

            $elementB = $elementB->next;
        }

        return $elementA;
    }

    private function getHash(ListNode $node): string
    {
        return spl_object_hash($node);
    }
}