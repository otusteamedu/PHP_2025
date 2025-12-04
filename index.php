<?php

namespace Index;

class Solution
{
    /**
     * @param array $list1
     * @param array $list2
     *
     * @return array
     */
    public function bubble(array $list1, array $list2): array
    {
        $merge = [...$list1, ...$list2];
        $count = count($merge);

        for ($i = 0; $i < $count - 1; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {
                $left = $merge[$i];
                $right = $merge[$j];

                if ($left > $right) {
                    $merge[$i] = $right;
                    $merge[$j] = $left;
                }
            }
        }

        return $merge;
    }
}

print_r(
    new Solution()->bubble([20, 2, 1, 3], [2, 3, 10, 3])
);
