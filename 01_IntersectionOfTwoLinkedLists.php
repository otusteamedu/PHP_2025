<?php

/**
 * Definition for a singly-linked list.
 * class ListNode {
 *     public $val = 0;
 *     public $next = null;
 *     function __construct($val) { $this->val = $val; }
 * }
 */

class Solution
{
    /**
     * @param ListNode $headA
     * @param ListNode $headB
     *
     * @return ListNode
     */
    function getIntersectionNode($headA, $headB)
    {
        // Проверка на нулевую длину
        if ($headA === NULL || $headB === NULL) {
            return NULL;
        }

        $a = $headA;
        $b = $headB;

		// Повторяем до тех пор, пока указатели не встретятся или не дойдут до конца связанных списков
        while ($a !== $b) {
			// Если указатель $a достигнет конца, переназначаем его в начало списка B
            if ($a === NULL) {
                $a = $headB;
            } else {
                $a = $a->next;
            }

	        // Если указатель $b достигнет конца, переназначаем его в начало списка A
            if ($b === NULL) {
                $b = $headA;
            } else {
                $b = $b->next;
            }
        }

        return $a;
    }
}