<?php
function twoSum($nums, $target) {
    $map = [];

    foreach ($nums as $index => $num) {
        $complement = $target - $num;

        if (isset($map[$complement])) {
            return [$map[$complement], $index];
        }

        $map[$num] = $index;
    }

    return [];
}

print_r(twoSum([2, 7, 11, 15], 9));
print_r(twoSum([3, 2, 4], 6));
print_r(twoSum([3, 3], 6));
?>