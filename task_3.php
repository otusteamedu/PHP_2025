<?php

/*Пересечение двух массивов
Даны два целочисленных массива nums1 и nums2, вернуть массив ихпересечение.
Каждый элемент в результате должен быть уникальным, и вы можете вернуть результат в любом порядке .
Пример 1:
Ввод: nums1 = [1,2,2,1], nums2 = [2,2]
Вывод: [2]

Пример 2:
Ввод: nums1 = [4,9,5], nums2 = [9,4,9,8,4]
Вывод: [9,4]
Пояснение: [4,9] также принимается.*/

function intersection(array  $nums1, array  $nums2): array
{
    $result = [];

    foreach ($nums1 as $num) {
        for ($i = 0; $i < count($nums2); $i++) {
            if (in_array($num, $result)) {
                continue;
            }
            if ($num === $nums2[$i]) {
                $result[] = $num;
                break;
            }
        }
    }

   return $result;
}

$result_1 = intersection([1,2,2,1], [2,2]);
$result_2 = intersection([4,9,5], [9,4,9,8,4]);

echo '<pre>';
print_r($result_1);
print_r($result_2);
echo '</pre>';