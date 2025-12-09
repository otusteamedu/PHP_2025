<?php
declare(strict_types=1);

namespace Pryaniki\App;
class ListNode {
    public $val = 0;
    public $next = null;
    function __construct($val = 0, $next = null) {
        $this->val = $val;
        $this->next = $next;
    }
    function addNode(int $val) {
        $this->next =  new ListNode($val);
    }
    static function initListFromList(array $arr): ?self
    {
        if (!$arr) {
            return null;
        }

        $head = new ListNode($arr[0]);
        $currNode = $head;
        foreach ($arr as $key => $item) {
            if($key === 0) {
                continue;
            }

            $node = new ListNode($item);
            $currNode->next = $node;
            $currNode = $currNode->next;
        }
        return $head;
    }

    static function printList(?ListNode $node) {
        while (!is_null($node)) {
            echo $node->val . ' ';
            $node = $node->next;
        }
       echo PHP_EOL;
    }

    static function test(): void {
        $tests = [];
        $tests[] = [
            'list1' => [1, 2, 4],
            'list2' => [1, 3, 4],
            'result' => [1, 1, 2, 3, 4, 4]
        ];

        foreach ($tests as $test) {
            self::testSolution($test);
        }
    }
    static function testSolution(array $test) {
        $solution = new Solution();
        $list1 = self::initListFromList($test['list1']);
        $list2 = self::initListFromList($test['list2']);
        $testResult = self::initListFromList($test['result']);
        $result = $solution->mergeTwoLists($list1, $list2);
        self::printList($testResult);
        self::printList($result);
    }
}