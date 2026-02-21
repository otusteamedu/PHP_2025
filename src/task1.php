class Solution {

    function getIntersectionNode($headA, $headB) {
        if ($headA === null || $headB === null) {
            return null;
        }
        
        $pointerA = $headA;
        $pointerB = $headB;
        
        // Продолжаем пока указатели не встретятся
        while ($pointerA !== $pointerB) {
            // Перемещаем указатель A на следующий узел или в начало списка B
            $pointerA = $pointerA !== null ? $pointerA->next : $headB;
            
            // Перемещаем указатель B на следующий узел или в начало списка A
            $pointerB = $pointerB !== null ? $pointerB->next : $headA;
        }
        
        // Возвращаем узел пересечения
        return $pointerA;
    }
}