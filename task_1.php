<?php

/*Сумма двух

Дан массив целых чисел nums и целое число target, вернуть индексы двух чисел, чтобы их сумма давала target .
Вы можете предположить, что каждый вход будет иметь ровно одно решение,
и вы не можете использовать один и тот же элемент дважды.
Вы можете возвращать ответ в любом порядке.

Пример 1:
Ввод: nums = [2,7,11,15], цель = 9
Вывод: [0,1]
Объяснение: поскольку nums[0] + nums[1] == 9, мы возвращаем [0, 1].

Пример 2:
Ввод: числа = [3,2,4], цель = 6
Вывод: [1,2]

Пример 3:
Ввод: числа = [3,3], цель = 6
Вывод: [0,1]*/

function twoSum(array $nums, int $target): array
{
    $hash = [];

    foreach ($nums as $key => $num) {
        for ($i = 0; $i < count($nums); $i++) {
            if ($key === $i) {
                continue;
            }
            if ($num + $nums[$i] === $target) {
                $hash[] = $key;
                $hash[] = $i;

                return $hash;
            }
        }
    }

    return [];
}

$result_1 = twoSum([2,7,11,15], 9);
$result_2 = twoSum([3,2,4], 6);
$result_3 = twoSum([3,3], 6);

echo '<pre>';
print_r($result_1);
print_r($result_2);
print_r($result_3);
echo '</pre>';

