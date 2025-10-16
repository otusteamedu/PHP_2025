/**
 * Definition for a singly-linked list.
 * class ListNode {
 *     public $val = 0;
 *     public $next = null;
 *     function __construct($val = 0, $next = null) {
 *         $this->val = $val;
 *         $this->next = $next;
 *     }
 * }
 */
class Solution {

    /**
     * @param ListNode $arList1
     * @param ListNode $arList2
     * @return ListNode
     */


    function listToArray($list) {
        $result = [];
        $current = $list;
        while ($current !== null) {
            $result[] = $current->val;
            $current = $current->next;
        }
        return $result;
    }

    function arrayToList($array) {
        if (empty($array)) return null;
        
        $head = new ListNode($array[0]);
        $current = $head;
        
        for ($i = 1; $i < count($array); $i++) {
            $current->next = new ListNode($array[$i]);
            $current = $current->next;
        }
        
        return $head;
    }


    function mergedArray($arMain,$listAbsorb){

        $arAbsorb = $this->listToArray($listAbsorb);

        $iLength = count($arAbsorb);

        for ($i = 0; $i < count($arAbsorb); $i++) {
        $arMain[] = $arAbsorb[$i];
        }

        return $arMain;

    }

    function mergeTwoLists($list1, $list2) {

        $arMerged = [];

        $arMerged = $this->mergedArray($arMerged,$list1);
        $arMerged = $this->mergedArray($arMerged,$list2);

        $totalLength = count($arMerged);

        for ($i = 0; $i < $totalLength - 1; $i++) {
            for ($j = 0; $j < $totalLength - $i - 1; $j++) {
                if ($arMerged[$j] > $arMerged[$j + 1]) {
                    $iTmp = $arMerged[$j];
                    $arMerged[$j] = $arMerged[$j + 1];
                    $arMerged[$j + 1] = $iTmp;
                }
            }
        }
        
        return $this->arrayToList($arMerged);   
        
    }
}
