<?php

namespace Igor\Test;

/**
 * Definition for singly-linked list node.
 */
class ListNode
{
    public $val = 0;
    public $next = null;

    public function __construct($val = 0, $next = null)
    {
        $this->val = $val;
        $this->next = $next;
    }

    /**
     * Создает связанный список из массива
     * 
     * @param array $values
     * @return ListNode|null
     */
    public static function fromArray(array $values): ?ListNode
    {
        if (empty($values)) {
            return null;
        }

        $head = new ListNode($values[0]);
        $current = $head;

        for ($i = 1; $i < count($values); $i++) {
            $current->next = new ListNode($values[$i]);
            $current = $current->next;
        }

        return $head;
    }

    /**
     * Преобразует связанный список в массив
     * 
     * @param ListNode|null $head
     * @return array
     */
    public static function toArray(?ListNode $head): array
    {
        $result = [];
        $current = $head;

        while ($current !== null) {
            $result[] = $current->val;
            $current = $current->next;
        }

        return $result;
    }
}
