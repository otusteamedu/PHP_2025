## Объяснение работы алгоритма:
1. Инициализация указателей: Начинаем с голов обоих списков
2. Перемещение указателей:
 * Каждый указатель идет по своему списку
 * Когда указатель достигает конца (становится null), он "перепрыгивает" в начало другого списка
3. Встреча указателей:
 * Если списки пересекаются, указатели встретятся в точке пересечения
 * Если не пересекаются, оба станут null одновременно

## Сложность
O(m + n), где m и n - длины списков

## Решение:
```php
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
    function getIntersectionNode($headA, $headB) {
        if ($headA === null || $headB === null) {
            return null;
        }
        
        $ptrA = $headA;
        $ptrB = $headB;

        while ($ptrA !== $ptrB) {
            $ptrA = ($ptrA === null) ? $headB : $ptrA->next;
            $ptrB = ($ptrB === null) ? $headA : $ptrB->next;
        }

        return $ptrA;
    }
}
```
