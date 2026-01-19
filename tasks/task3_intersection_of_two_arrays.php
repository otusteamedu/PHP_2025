<?php

/**
 * Получается временная сложность (создание хэш-таблиц array_unique и поиск через array_intersect)
 */

class Solution {

    /**
     * @param Integer[] $nums1
     * @param Integer[] $nums2
     * @return Integer[]
     */
    function intersection($nums1, $nums2) {
         return array_unique(array_intersect($nums1, $nums2));
    }
}