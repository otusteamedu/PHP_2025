<?php
declare(strict_types=1);

class Solution
{

    /**
     * @param ListNode $list1
     * @param ListNode $list2
     *
     * @return ListNode
     */
    function mergeTwoLists($list1, $list2)
    {
        $head = new ListNode(null);
        $this->recursiveChainBuilder($head, $list1, $list2);

        return $head;
    }

    public function recursiveChainBuilder(?ListNode $listNode, ?ListNode $val1, ?ListNode $val2)
    {
        if (isset($val1->next) || isset($val2->next) || (isset($val1) && isset($val2))) {
            $listNode->next = new ListNode();
        }

        if (isset($val1) && (!isset($val2) || $val1->val <= $val2->val)) {
            $listNode->val = $val1->val;

            return $this->recursiveChainBuilder($listNode->next, $val1->next, $val2);
        }
        if (isset($val2) && (!isset($val1) || $val2->val <= $val1->val)) {
            $listNode->val = $val2->val;

            return $this->recursiveChainBuilder($listNode->next, $val1, $val2->next);
        }

        return null;
    }
}
