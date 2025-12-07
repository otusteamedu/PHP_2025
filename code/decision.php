<?php

declare(strict_types=1);

class ListNode {
    public $val = 0;
    public $next = null;
    function __construct($val = 0, $next = null) {
        $this->val = $val;
        $this->next = $next;
    }
}
class Solution {
    function mergeTwoLists(ListNode|null $list1, ListNode|null $list2): ListNode|null
    {
        if (is_null($list1)) return $list2;
        if (is_null($list2)) return $list1;

        if($list1->val > $list2->val) {
            return $this->getHeadMergedLists($list2, $list1);
        }

        return $this->getHeadMergedLists($list1, $list2);
    }

    function getHeadMergedLists(ListNode $mainListNode, ListNode $otherListNode): ListNode
    {
        $resultNode = $mainListNode;

        if ($this->isLastNode($mainListNode)) {
            $this->attachOtherListToMain($otherListNode, $mainListNode);
            return $resultNode;
        }

        $otherListNodeIsLast = $this->isLastNode($otherListNode);

        if ($otherListNodeIsLast &&
            $mainListNode->val <= $otherListNode->val) {
            $this->insertNodeToList($otherListNode, $mainListNode);
            return $resultNode;
        }

        return $resultNode;
    }
    function isLastNode(ListNode $node): bool
    {
        return is_null($node->next);
    }

    function attachOtherListToMain(ListNode $otherListNode, ListNode &$mainListNode): void
    {
        $mainListNode->next = $otherListNode;
    }

    function insertNodeToList(ListNode $node, ListNode &$mainListNode): void
    {
        $nextMainNode = $mainListNode->next;
        $node->next = $nextMainNode;
        $mainListNode->next = $node;
    }
}