<?
class Solution {

    /**
     * https://leetcode.com/problems/intersection-of-two-arrays/description/
     * @param Integer[] $nums1
     * @param Integer[] $nums2
     * @return Integer[]
     */
    function intersection($nums1, $nums2) {
        $hash = [];
        foreach ($nums1 as $number) {
            $hash[$number] = true;
        }

        $result = [];
        foreach ($nums2 as $number) {
            if (isset($hash[$number]) && !isset($result[$number])) {
                $result[$number] = $number;
            }
        }
        return $result;
    }
}