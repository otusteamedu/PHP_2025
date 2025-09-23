### Задача: https://leetcode.com/problems/largest-positive-integer-that-exists-with-its-negative/description/

## Решение:
```php
class Solution {

    /**
     * @param Integer[] $nums
     * @return Integer
     */
    function findMaxK($nums) {
        $seen = [];
        $ans = -1;

        foreach ($nums as $num) {
            $seen[$num] = true;
        }

        foreach ($nums as $num) {
            $neg = -$num;
            if (isset($seen[$neg])) {
                $absNum = abs($num);
                if ($absNum > $ans) {
                    $ans = $absNum;
                }
            }
        }
        
        return $ans;
    }
}
```
Сложность: O(n) - где n длина массива
