<?php
require __DIR__ . '/vendor/autoload.php';


// Q1 - https://leetcode.com/problems/two-sum/description/
/*
[$n1,$t1] = [[2,7,11,15],9];
$start = microtime( true );
$res1 = [twoSum($n1,$t1),sprintf( '%.6f sec.', microtime( true ) - $start )];

[$n2, $t2] = [[3,2,4],6];
$start = microtime( true );
$res2 = [twoSum($n2,$t2),sprintf( '%.6f sec.', microtime( true ) - $start )];

[$n3,$t3] = [[3,3],6];
$start = microtime( true );
$res3 = [twoSum($n3,$t3),sprintf( '%.6f sec.', microtime( true ) - $start )];
*/

function twoSum($nums, $target) {
    $result = [];
    $map = [];
    foreach ($nums as $key => $num) {
        $complement = $target - $num;
        if (isset($map[$complement])) {
            return [$map[$complement], $key];
        }
        $map[$num] = $key;
    }
}

// Q2 - https://leetcode.com/problems/sort-array-by-increasing-frequency/description/

/*
$n1 = [1,1,2,2,2,3];
$start = microtime( true );
$res1 = [frequencySort($n1),sprintf( '%.6f sec.', microtime( true ) - $start )];

$n2 = [2,3,1,3,2];
$start = microtime( true );
$res2 = [frequencySort($n2),sprintf( '%.6f sec.', microtime( true ) - $start )];

$n3 = [-1,1,-6,4,5,-6,1,4,1];
$start = microtime( true );
$res3 = [frequencySort($n3),sprintf( '%.6f sec.', microtime( true ) - $start )];


$n4 = [-8,7,-1,3,5,7,-8,-8,0];
$res4 = [frequencySort($n4),sprintf( '%.6f sec.', microtime( true ) - $start )];*/

function frequencySort($nums) {
    $arHash = [];
    $result = [];
    foreach ($nums as $num) {
        $arHash[$num]++;
    }

    asort($arHash);
    // Группируем по значению
    $groupedByValue = [];
    foreach ($arHash as $num => $val) {
        $groupedByValue[$val][] = $num;
    }

    // Sort frequencies (already sorted by asort, but ensures order)
    ksort($groupedByValue);

    foreach ($groupedByValue as $val => $numbers) {
        rsort($numbers);
        foreach ($numbers as $num) {
            for ($i = 0; $i < $val; $i++) {
                $result[] = $num;
            }
        }
    }
    return $result;
}


// Q3 - https://leetcode.com/problems/intersection-of-two-arrays/description/

/*
[$n1, $n2] = [[1,2,2,1],[2,2]];
$start = microtime( true );
$res1 = [intersection($n1,$n2),sprintf( '%.6f sec.', microtime( true ) - $start )];

[$n1, $n2] = [[4,9,5],[9,4,9,8,4]];
$start = microtime( true );
$res2 = [intersection($n1,$n2),sprintf( '%.6f sec.', microtime( true ) - $start )];
*/

function intersection($nums1, $nums2) {
    $set1 = array_flip($nums1);
    $set2 = array_flip($nums2);

    return array_keys(array_intersect_key($set1, $set2));
}

// Q4 -  https://leetcode.com/problems/largest-positive-integer-that-exists-with-its-negative/description/

/*$n1 = [-1,2,-3,3];
$start = microtime( true );
$res1 = [findMaxK($n1),sprintf( '%.6f sec.', microtime( true ) - $start )];

$n2 = [-1,10,6,7,-7,1];
$start = microtime( true );
$res2 = [findMaxK($n2),sprintf( '%.6f sec.', microtime( true ) - $start )];

$n3 = [-10,8,6,7,-2,-3];
$start = microtime( true );
$res3 = [findMaxK($n3),sprintf( '%.6f sec.', microtime( true ) - $start )];

$n4  = [-37,37,-9,2,47,18,13,-11,9,-28];
$start = microtime( true );
$res4 = [findMaxK($n4),sprintf( '%.6f sec.', microtime( true ) - $start )];
*/

function findMaxK($nums) {
    $max = -1; // default
    $flipArr = array_flip($nums);

    foreach ($nums as $num) {
        if (isset($flipArr[-$num])) {
            $max = max($max, abs($num));
        }
    }

    return $max;
}


// Q5 -  https://leetcode.com/problems/how-many-numbers-are-smaller-than-the-current-number/description/
/*
$n1 = [8,1,2,2,3];
$start = microtime( true );
$res1 = [smallerNumbersThanCurrent($n1),sprintf( '%.6f sec.', microtime( true ) - $start )];

$n2 = [6,5,4,8];
$start = microtime( true );
$res2 = [smallerNumbersThanCurrent($n2),sprintf( '%.6f sec.', microtime( true ) - $start )];

$n3 = [7,7,7,7];
$start = microtime( true );
$res3 = [smallerNumbersThanCurrent($n3),sprintf( '%.6f sec.', microtime( true ) - $start )];
*/

function smallerNumbersThanCurrent($nums) {
    $sortedNums = $nums;
    sort($sortedNums);
    $map = [];
    foreach ($sortedNums as $key => $num) {
        if (!isset($map[$num])) {
            $map[$num] = $key;
        }
    }
    return array_map(function ($num) use ($map) {
        return $map[$num];
    }, $nums);
}