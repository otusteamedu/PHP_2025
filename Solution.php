<?php

declare(strict_types=1);

class Solution
{
    /**
     * https://leetcode.com/problems/two-sum/description/
     * @param Integer[] $nums
     * @param Integer $target
     * @return Integer[]
     */
    public static function twoSum(array $nums, int $target): array
    {
        $result = [];
        $hash = [];
        for ($i = 0; $i < count($nums); $i++) {
            $complement = $target - $nums[$i];
            if (array_key_exists($complement, $hash)) {
                return [$hash[$complement], $i];
            }
            $hash[$nums[$i]] = $i;
        }
        return $result;
    }

    /**
     * https://leetcode.com/problems/sort-array-by-increasing-frequency/description/
     * @param Integer[] $nums
     * @return Integer[]
     */
    public static function frequencySort(array $nums): array
    {
        $frequency = [];
        foreach($nums as $num) {
            if (!array_key_exists($num, $frequency)) {
                $frequency[$num] = 1;
            } else {
                $frequency[$num] += 1;
            }
        }

        usort($nums, function ($a, $b) use ($frequency) {
           if ($frequency[$a] !== $frequency[$b]) {
               return $frequency[$a] <=> $frequency[$b];
           }
           return $b <=> $a;
        });

        return $nums;
    }

    /**
     * https://leetcode.com/problems/intersection-of-two-arrays/description/
     * @param Integer[] $nums1
     * @param Integer[] $nums2
     * @return Integer[]
     */
    public static function intersection(array $nums1, array $nums2): array
    {
        $nums1Count = count($nums1);
        $nums2Count = count($nums2);
        $result = [];

        if ($nums2Count < $nums1Count) {
            for ($i = 0; $i < $nums2Count; $i++) {
                if (in_array($nums2[$i], $nums1)) {
                    $result[] = $nums2[$i];
                }
            }
        } else {
            for ($i = 0; $i < $nums1Count; $i++) {
                if (in_array($nums1[$i], $nums2)) {
                    $result[] = $nums1[$i];
                }
            }
        }
        return array_unique($result);
    }

    /**
     * https://leetcode.com/problems/largest-positive-integer-that-exists-with-its-negative/description/
     * @param Integer[] $nums
     * @return Integer
     */
    public static function findMaxK(array $nums): int {

        $positiveNumbers = array_filter($nums, function ($num) {
            return $num > 0;
        });

        $negativeNumbers = array_filter($nums, function ($num) {
            return $num < 0;
        });

        if (count($positiveNumbers) === 0) {
            return -1;
        }

        rsort($positiveNumbers);
        for($i = 0; $i < count($positiveNumbers); $i++) {
            if (in_array($positiveNumbers[$i] * -1, $negativeNumbers)) {
                return $positiveNumbers[$i];
            }
        }

        return -1;
    }

    /**
     * https://leetcode.com/problems/how-many-numbers-are-smaller-than-the-current-number/description/
     * @param Integer[] $nums
     * @return Integer[]
     */
    public static function smallerNumbersThanCurrent(array $nums): array
    {
        $result = [];
        $sorted = $nums;
        sort($sorted);
        $countMap = [];

        foreach ($sorted as $index => $num) {
            if (!array_key_exists($num, $countMap)) {
                $countMap[$num] = $index;
            }
        }

        foreach ($nums as $num) {
            $result[] = $countMap[$num];
        }

        return $result;
    }
}
