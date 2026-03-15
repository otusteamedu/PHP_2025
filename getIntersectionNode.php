<?php
class ListNode {
    public $val;
    public $next;

    function __construct($val = 0, $next = null) {
        $this->val = $val;
        $this->next = $next;
    }
}

class Solution {
    /**
    * @param ListNode $headA
    * @param ListNode $headB
    * @return ListNode
    */
    function getIntersectionNode($headA, $headB) {
        $list1 = $headA;

        while ($list1 != null) {
            $list2 = $headB;

            while ($list2 != null) {
                if ($list2 === $list1) {
                     return $list2;
                }
                $list2 = $list2->next;
            }

            $list1 = $list1->next;
        }

        return null;
    }
}

$a1 = new ListNode(2);
$a2 = new ListNode(3);
$a1->next = $a2;

$b1 = new ListNode(1);
$b1->next = $a1;

$c1 = new ListNode(4);
$c2 = new ListNode(5);
$c3 = new ListNode(6);
$c4 = new ListNode(7);
$c5 = new ListNode(8);
$c1->next = $c2;
$c2->next = $c3;
$c3->next = $c4;
$c4->next = $c5;
$c5->next = $a1;

$solution = new Solution();
$intersection = $solution->getIntersectionNode($b1, $c1);

//O(n * m)
//Я понимаю, что есть более оптимальные решения (метод двух указателей),
//но это только такую рабочую реализацию смогла придумать сама :)