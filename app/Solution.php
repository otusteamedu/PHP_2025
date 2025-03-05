<?php

namespace app;

class Solution
{
    /**
     * Объединяет два отсортированных связанных списка в один отсортированный список.
     *
     * @param ListNode|null $list1
     * @param ListNode|null $list2
     * @return ListNode|null
     */
    public function mergeTwoLists(?ListNode $list1, ?ListNode $list2): ?ListNode
    {
        // Создаем фиктивный узел
        $dummy = new ListNode();
        $current = $dummy;

        // Пока оба списка не пусты
        while ($list1 !== null && $list2 !== null) {
            if ($list1->val < $list2->val) {
                $current->next = $list1;
                $list1 = $list1->next;
            } else {
                $current->next = $list2;
                $list2 = $list2->next;
            }
            $current = $current->next;
        }

        // Если один из списков закончился, добавляем оставшиеся узлы другого списка
        if ($list1 !== null) {
            $current->next = $list1;
        } else {
            $current->next = $list2;
        }

        // Возвращаем заголовок нового списка (после фиктивного узла)
        return $dummy->next;
    }
}