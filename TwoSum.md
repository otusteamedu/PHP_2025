### Задача: https://leetcode.com/problems/two-sum/description/

## Решение:
```php
class Solution {

    /**
     * @param Integer[] $nums
     * @param Integer $target
     * @return Integer[]
     */
    function twoSum($nums, $target) {
        $map = [];
        
        for ($i = 0; $i < count($nums); $i++) {
            $current = $nums[$i];
            $needed = $target - $current;

            if (isset($map[$needed])) {
                return [$map[$needed], $i];
            }

            $map[$current] = $i;
        }
        
        return [];
    }
}
```
Сложность: O(n) - т.к. проходим по массиву один раз
