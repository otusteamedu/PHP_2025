### Задача:
https://leetcode.com/problems/sort-array-by-increasing-frequency/description/

## Решение:
```php
class Solution {

    /**
     * @param Integer[] $nums
     * @return Integer[]
     */
    function frequencySort($nums) {
        $frequencyMap = [];
        foreach ($nums as $num) {
            $frequencyMap[$num] = isset($frequencyMap[$num]) ? $frequencyMap[$num] + 1 : 1;
        }

        usort($nums, function($a, $b) use ($frequencyMap) {
            $freqA = $frequencyMap[$a];
            $freqB = $frequencyMap[$b];

            if ($freqA < $freqB) {
                return -1;
            } elseif ($freqA > $freqB) {
                return 1;
            }

            if ($a > $b) {
                return -1;
            } elseif ($a < $b) {
                return 1;
            }
            
            return 0;
        });
        
        return $nums;
    }
}
```

Сложность: O(n log n) - из-за сортировки
