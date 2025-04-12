<?php

/*Сколько чисел меньше текущего числа

Учитывая массив nums, для каждого nums[i] найдите, сколько чисел в массиве меньше его.
То есть, для каждого nums[i]нужно посчитать количество допустимых j's таких, что  j != i и nums[j] < nums[i] .
Верните ответ в виде массива.

Пример 1:

Ввод: nums = [8,1,2,2,3]
 Вывод: [4,0,1,1,3]
 Пояснение:
Для nums[0]=8 существуют четыре числа, меньших его (1, 2, 2 и 3).
Для nums[1]=1 не существует числа, меньшего, чем оно.
Для nums[2]=2 существует одно число, меньшее его (1).
Для nums[3]=2 существует одно число, меньшее его (1).
Для nums[4]=3 существуют три числа, меньших его (1, 2 и 2).

Пример 2:
Ввод: числа = [6,5,4,8]
Вывод: [2,1,0,3]

Пример 3:
Ввод: числа = [7,7,7,7]
Вывод: [0,0,0,0]*/

function smallerNumbersThanCurrent(array $nums): array
{
    $hash = [];
    foreach ($nums as $num) {
        $k = 0;
        for ($i = 0; $i < count($nums); $i++) {
            if ($num > $nums[$i]) {
                $k++;
            }
        }
        $hash[] = $k;
    }

    return $hash;
}

$result_1 = smallerNumbersThanCurrent([8,1,2,2,3]);
$result_2 = smallerNumbersThanCurrent([6,5,4,8]);
$result_3 = smallerNumbersThanCurrent([7,7,7,7]);

echo '<pre>';
print_r($result_1);
print_r($result_2);
print_r($result_3);
echo '</pre>';
