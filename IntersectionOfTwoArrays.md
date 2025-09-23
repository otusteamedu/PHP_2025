### Задача: https://leetcode.com/problems/intersection-of-two-arrays/description/

## Решение:
```php
class Solution {

    /**
     * @param Integer[] $nums1
     * @param Integer[] $nums2
     * @return Integer[]
     */
    function intersection($nums1, $nums2) {
        sort($nums1);
        sort($nums2);
        
        $i = $j = 0;
        $result = [];
        $prev = null;
        
        while ($i < count($nums1) && $j < count($nums2)) {
            if ($nums1[$i] == $nums2[$j]) {
                if ($nums1[$i] !== $prev) {
                    $result[] = $nums1[$i];
                    $prev = $nums1[$i];
                }
                $i++;
                $j++;
            } elseif ($nums1[$i] < $nums2[$j]) {
                $i++;
            } else {
                $j++;
            }
        }
        
        return $result;
    }
}
```
Сложность: O(n + m) - где n и m длины массивов
