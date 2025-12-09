<?php

namespace Pryaniki\App;

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
        while (!is_null($otherListNode)) {
            if ($this->isLastNode($mainListNode)) {
                $this->attachOtherListToMain($otherListNode, $mainListNode);
                break;
            }

            if ($mainListNode->val <= $otherListNode->val &&
                $mainListNode->next->val >= $otherListNode->val) {
                $this->insertNodeToList($otherListNode, $mainListNode);
            } else {
                $mainListNode = $mainListNode->next;
            }
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

    function insertNodeToList(ListNode &$node, ListNode &$mainListNode): void
    {
        $nextOtherNode = $node->next;
        $nextMainNode = $mainListNode->next;

        $mainListNode->next = $node;
        $node->next = $nextMainNode;

        $node = $nextOtherNode;
    }
}