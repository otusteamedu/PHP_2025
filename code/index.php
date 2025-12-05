<?php
require __DIR__ . '/vendor/autoload.php';
use Ak\Hw\Services\ArrayMerge;

$merge = new ArrayMerge();

$test1 = $merge([1,2,4], [1,3,4]);
$test2 = $merge([-4,-1,2,], [-3,1,5,6]);
$test3 = $merge([1,4,3], [1,2,5,6]);
$test4 = $merge([], []);
$test5 = $merge([], [0]);

echo "<pre>";
print_r([$test1, $test2, $test3, $test4, $test5]);
echo "</pre>";

?>