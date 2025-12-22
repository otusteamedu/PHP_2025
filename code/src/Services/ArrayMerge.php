<?php
namespace Ak\Hw\Services;

class ArrayMerge
{
    /**
     * Объединяет два массива в один.
     *
     * @param array $array1 Первый массив.
     * @param array $array2 Второй массив.
     * @return array Объединенный и отсортированный массив.
     */
    public function __invoke(array $array1, array $array2):array
    {
        $result = array();

        // Формирование переменных
        [$count1, $count2, $i, $j] = [count($array1), count($array2), 0, 0];

        if ($count1 === 0 && $count2 === 0) {
            return $result;
        }

        // Сортировка массивов
        do {
            if ($i < $count1 && ($j >= $count2 || $array1[$i] < $array2[$j]) ) {
                $result[] = $array1[$i++];
            } elseif ($j < $count2 && ($i >= $count1 || $array2[$j] <= $array1[$i])) {
                $result[] = $array2[$j++];
            }
        } while ($i < $count1 || $j < $count2);

        return $result;
    }
}