<?php

declare(strict_types=1);

namespace App\Array;

class Helpers
{
    /**
     * @param ListNode|null $list1
     * @param ListNode|null $list2
     * @return ListNode|null
     */
    public static function mergeTwoLists(listNode|null $list1, listNode|null $list2): listNode|null
    {
        $newListNode = new ListNode();
        $currentNode = $newListNode;

        while ($list1 !== null && $list2 !== null) {
            if ($list1->val <= $list2->val) {
                $currentNode->next = $list1;
                $list1 = $list1->next;
            } else {
                $currentNode->next = $list2;
                $list2 = $list2->next;
            }

            $currentNode = $currentNode->next;
        }

        $currentNode->next = $list1 !== null ? $list1 : $list2;

        return $newListNode->next;
    }

    /**
     * @param ListNode|null $list
     * @return string
     */
    public static function toStringListNode(ListNode|null $list): string
    {
        $array = [];

        while ($list !== null) {
            $array[] = $list->val;
            $list = $list->next;
        }

        return '[' . \implode(', ', $array) . ']';
    }
}
