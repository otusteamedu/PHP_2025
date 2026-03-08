<?php

namespace Igor\Test\LeetCode;

/**
 * Задача: Intersection of Two Arrays
 * 
 * Условие: Даны два массива nums1 и nums2, вернуть массив их пересечения.
 * Каждый элемент в результате должен быть уникальным, результат может быть в любом порядке.
 * 
 * @link https://leetcode.com/problems/intersection-of-two-arrays/
 */
class IntersectionOfTwoArrays
{
    /**
     * Находит пересечение двух массивов
     * 
     * Алгоритм:
     * 1. Преобразуем первый массив в множество (hash set) для O(1) поиска
     * 2. Проходим по второму массиву и проверяем наличие элементов в множестве
     * 3. Используем дополнительное множество для результата, чтобы избежать дубликатов
     * 
     * Временная сложность: O(n + m), где n = |nums1|, m = |nums2|
     * - Создание множества из nums1: O(n)
     * - Проход по nums2: O(m)
     * - Проверка наличия в множестве: O(1) в среднем
     * 
     * Пространственная сложность: O(min(n, m))
     * - Множество для nums1: O(n)
     * - Множество для результата: O(min(n, m)) в худшем случае
     * 
     * @param int[] $nums1
     * @param int[] $nums2
     * @return int[]
     */
    public function intersection(array $nums1, array $nums2): array
    {
        // Граничный случай: если один из массивов пуст
        if (empty($nums1) || empty($nums2)) {
            return [];
        }

        // Создаем множество из первого массива для быстрого поиска
        // Используем array_flip для создания ассоциативного массива (эмуляция Set)
        $set1 = array_flip($nums1);
        
        // Множество для результата (используем ключи для уникальности)
        $resultSet = [];

        // Проходим по второму массиву
        foreach ($nums2 as $num) {
            // Если элемент есть в первом множестве и еще не добавлен в результат
            if (isset($set1[$num]) && !isset($resultSet[$num])) {
                $resultSet[$num] = $num;
            }
        }

        // Возвращаем значения массива (убираем ключи)
        return array_values($resultSet);
    }

    /**
     * Альтернативное решение с использованием array_intersect
     * 
     * Временная сложность: O(n * m) в худшем случае
     * Пространственная сложность: O(min(n, m))
     * 
     * Менее эффективно, но более простое
     */
    public function intersectionAlternative(array $nums1, array $nums2): array
    {
        // Убираем дубликаты из обоих массивов
        $nums1 = array_unique($nums1);
        $nums2 = array_unique($nums2);
        
        // Находим пересечение
        return array_values(array_intersect($nums1, $nums2));
    }
}
