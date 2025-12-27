<?php
function frequencySort($nums) {
    $frequency = array_count_values($nums);
    
    usort($nums, function($a, $b) use ($frequency) {

        if ($frequency[$a] === $frequency[$b]) {
            return $b - $a;
        }
        return $frequency[$a] - $frequency[$b];
    });
    
    return $nums;
}

print_r(frequencySort([1, 1, 2, 2, 2, 3]));
print_r(frequencySort([2, 3, 1, 3, 2]));
print_r(frequencySort([-1, 1, -6, 4, 5, -6, 1, 4, 1]));
?>