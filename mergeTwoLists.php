<?php 

class ListNode {
    public $val = 0;
    public $next = null;
    function __construct($val = 0, $next = null) {
        $this->val = $val;
        $this->next = $next;
    }
}

/*
Решение получилось довольно оптимальным O(N) по времени
и O(1) по памяти за счет того что результирующий список 
собирается из исходных узлов без пересоздания узлов.  
*/

class Solution {
    /**
     * @param ListNode $list1
     * @param ListNode $list2
     * @return ListNode
     */
    function mergeTwoLists($list1, $list2) {
        //проверим на пустые значения
        if ($list1 === null) return $list2;
        if ($list2 === null) return $list1;

        //Добавим первый элемент
        if ($list1->val > $list2->val) {
            $result = $list2;
            $currentSecond = $list2->next;
            $currentFirst = $list1;
        } else {
            $result = $list1;
            $currentFirst = $list1->next;
            $currentSecond = $list2;
        };
        $tail = $result;  //$tail - хвост результирующего списка

        //в цикле добавим вторые и последующие
        while ($currentFirst !== null && $currentSecond !== null) {
            if ($currentFirst->val > $currentSecond->val) {
                $tail->next = $currentSecond; //записываем очередной узел
                $currentSecond = $currentSecond->next; //после того как узел записан, сдвигаем указатель на следующий узел
            } else {
                $tail->next = $currentFirst;
                $currentFirst = $currentFirst->next;
            }
            $tail = $tail->next;
        }

        //если остался хвост одного из списков - добавляем без условий
        if ($currentFirst !== null) {
            $tail->next = $currentFirst;
        } elseif ($currentSecond !== null) {
            $tail->next = $currentSecond;
        }

        return $result;
    }
}

// Тест
$test = new Solution;
$listFirst = new ListNode(5);
$listSecond = new ListNode(1, new ListNode(2, new ListNode(4)));
$mergedList = $test->mergeTwoLists($listFirst, $listSecond);

// Вывод результата
function printList($node) {
    while ($node !== null) {
        echo $node->val . " -> ";
        $node = $node->next;
    }
    echo "null\n";
}

printList($mergedList);
?>
