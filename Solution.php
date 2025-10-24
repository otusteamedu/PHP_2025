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


    function mergeTwoLists($list1, $list2) {

        $arMerged = [];

        $arMerged1 = $this->listToArray($list1);
        $arMerged2 = $this->listToArray($list2);

        $iLength1 = count($arMerged1);
        $iLength2 = count($arMerged2);

        $iCounter1 = 0;
        $iCounter2 = 0;
        $arMergedRes = [];
 
        for ($i = 0 ; $i < $iLength1 + $iLength2; $i++){

            if ($iCounter1 >= $iLength1) {
                $arMergedRes[] = $arMerged2[$iCounter2];
                $iCounter2++;
            }elseif($iCounter2 >= $iLength2) {
                $arMergedRes[] = $arMerged1[$iCounter1];
                $iCounter1++;
            }elseif($arMerged1[$iCounter1] <= $arMerged2[$iCounter2]) {
            $arMergedRes[] = $arMerged1[$iCounter1];
            $iCounter1++;
            }else{
            $arMergedRes[] = $arMerged2[$iCounter2];
            $iCounter2++;
            }

        }


        return $this->arrayToList($arMergedRes);   
        
    }
}
