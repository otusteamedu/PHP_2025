<?php

/*Сортировка массива по возрастанию частоты

Дан массив целых чисел nums, отсортируйте массив в порядке возрастания на основе частоты значений.
Если несколько значений имеют одинаковую частоту, отсортируйте их в порядке убывания.

Верните отсортированный массив .

Пример 1:
Ввод: nums = [1,1,2,2,2,3]
Вывод: [3,1,1,2,2,2]
Пояснение: «3» имеет частоту 1, «1» имеет частоту 2, а «2» имеет частоту 3.

Пример 2:
Ввод: nums = [2,3,1,3,2]
Вывод: [1,3,3,2,2]
Пояснение: «2» и «3» оба имеют частоту 2, поэтому они сортируются в порядке убывания.

Пример 3:
Ввод: числа = [-1,1,-6,4,5,-6,1,4,1]
Вывод: [5,-1,4,4,-6,-6,1,1,1]*/

function sortArray(array $nums): array
{
    $hash = [];

    foreach ($nums as $num) {
        if (isset($hash[$num])) {
            $hash[$num]++;
        } else {
            $hash[$num] = 1;
        }
    }

    $countedArray = [];
    foreach ($hash as $num => $freq) {
        $countedArray[] = [$num, $freq];
    }

    for ($i = 0; $i < count($countedArray); $i++) {
        for ($j = 0; $j < count($countedArray) - 1; $j++) {
            if ($countedArray[$j][1] > $countedArray[$j + 1][1] ||
                ($countedArray[$j][1] == $countedArray[$j + 1][1] &&
                    $countedArray[$j][0] < $countedArray[$j + 1][0])) {

                $temp = $countedArray[$j];
                $countedArray[$j] = $countedArray[$j + 1];
                $countedArray[$j + 1] = $temp;
            }
        }
    }

    $sortedNums = [];
    foreach ($countedArray as $item) {
        for ($i = 0; $i < $item[1]; $i++) {
            $sortedNums[] = $item[0];
        }
    }

    return $sortedNums;
}


$result_1 = sortArray([1, 1, 2, 2, 2, 3]);
$result_2 = sortArray([2,3,1,3,2]);
$result_3 = sortArray([-1, 1, -6, 4, 5, -6, 1, 4, 1]);

echo '<pre>';
print_r($result_1);
print_r($result_2);
print_r($result_3);
echo '</pre>';