<?php
function intersection($nums1, $nums2) {
    $set1 = array_unique($nums1);
    $result = [];

    foreach ($nums2 as $num) {
        if (in_array($num, $set1) && !in_array($num, $result)) {
            $result[] = $num;
        }
    }

    return $result;
}

print_r(intersection([1, 2, 2, 1], [2, 2]));
print_r(intersection([4, 9, 5], [9, 4, 9, 8, 4]));
?>