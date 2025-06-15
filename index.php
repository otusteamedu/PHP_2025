<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\App;

$emailsToCheck = [
    "test@example.com",
    "invalid-email",
    "user@unrealdomain.xyz"
];

$app = new App();
echo $app->run($emailsToCheck);






class ListNode {
    public $val = 0;
    public $next = null;
    function __construct($val = 0, $next = null) {
        $this->val = $val;
        $this->next = $next;
    }
}

class Solution {
    /**
     * @param ListNode $list1
     * @param ListNode $list2
     * @return ListNode
     */
    function mergeTwoLists($list1, $list2) {

        $emptyNode = new ListNode();
        $current = $emptyNode;

        while ($list1 !== null && $list2 !== null) {
            if ($list1->val <= $list2->val) {
                $current->next = $list1;
                $list1 = $list1->next;
            } else {
                $current->next = $list2;
                $list2 = $list2->next;
            }
            $current = $current->next;
        }

        if ($list1 !== null) {
            $current->next = $list1;
        } else {
            $current->next = $list2;
        }

        return $emptyNode->next;
    }
}

function createLinkedList($arr) {
    $emptyNode = new ListNode();
    $current = $emptyNode;
    foreach ($arr as $val) {
        $current->next = new ListNode($val);
        $current = $current->next;
    }
    return $emptyNode->next;
}

function linkedListToArray($head) {
    $arr = [];
    while ($head !== null) {
        $arr[] = $head->val;
        $head = $head->next;
    }
    return $arr;
}

// Тестирование
$list1 = createLinkedList([1, 2, 4]);
$list2 = createLinkedList([1, 3, 4]);

$solution = new Solution();
$mergedList = $solution->mergeTwoLists($list1, $list2);
$resultArray = linkedListToArray($mergedList);

print_r($resultArray);

