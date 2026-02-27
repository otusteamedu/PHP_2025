<?php
class Solution {

    function twoSum($nums, $target) {
        $countNums = count($nums);
        $nums2 = $nums;
        $result = [];
        for($i = 0; $i < $countNums; $i++){
            unset($nums2[$i]);
            if (in_array(($target - $nums[$i]), $nums2)){
                $result[$i] = 1;
                $flip = array_flip($nums2);
                $j = $flip[($target - $nums[$i])];
                $result[$j] = 1;
                return array_keys($result);                
            }
        }
        return [];
    }

    function frequencySort($nums) {
            $resFreq = [];
            foreach ($nums as $num) {
                $resFreq[$num] = ++$resFreq[$num];
            }
            usort($nums, function($a, $b) use ($resFreq) {
                if ($resFreq[$a] == $resFreq[$b]) {
                    return $b <=> $a;
                }
                return $resFreq[$a] <=> $resFreq[$b];
            });
           
            return $nums;    
    }

    function intersection($nums1, $nums2) {
        $res = [];
        foreach($nums1 as $num){
            if (in_array($num, $nums2)){
                $res[$num] = 1;
            }
        }
        return array_keys($res);
    }

    function findMaxK($nums) {
        arsort($nums);
        forEach($nums as $num){
            if ($num > 0 && in_array(-$num, $nums)){
                return $num;
            }
        }
        return -1;
    }

    function smallerNumbersThanCurrent($nums) {
        $count = count($nums);
        $countValues = array_count_values($nums);
        $nums2 = $nums;
        arsort($nums2);
        $i = 0;
        $val = [];
        forEach($nums2 as $key => $value){
            if (array_key_exists($value, $val)){
                $i = $i + 1;
                continue;
            }            
            $val[$value] = $count - $i - $countValues[$value];
            $i = $i + 1;
        }
        $res = [];
        forEach($nums as $key => $value){
            $res[$key] = $val[$value];
        }
        return array_values($res);
    }          
}