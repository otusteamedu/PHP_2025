<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Igor\Test\LeetCode\IntersectionOfTwoArrays;
use Igor\Test\LeetCode\LargestPositiveInteger;
use Igor\Test\LeetCode\TwoSum;
use Igor\Test\LeetCode\SortArrayByFrequency;

/**
 * Тесты для всех LeetCode задач
 */
class LeetCodeTest
{
    private int $passed = 0;
    private int $failed = 0;

    public function runAllTests(): void
    {
        echo "Запуск тестов для LeetCode задач\n";
        echo str_repeat("=", 70) . "\n\n";

        $this->testIntersectionOfTwoArrays();
        $this->testLargestPositiveInteger();
        $this->testTwoSum();
        $this->testSortArrayByFrequency();

        echo "\n" . str_repeat("=", 70) . "\n";
        echo "Результаты: {$this->passed} пройдено, {$this->failed} провалено\n";
    }

    private function testIntersectionOfTwoArrays(): void
    {
        echo "1. Intersection of Two Arrays\n";
        echo str_repeat("-", 70) . "\n";

        $solution = new IntersectionOfTwoArrays();

        $tests = [
            [
                'nums1' => [1, 2, 2, 1],
                'nums2' => [2, 2],
                'expected' => [2],
                'name' => 'Базовый тест'
            ],
            [
                'nums1' => [4, 9, 5],
                'nums2' => [9, 4, 9, 8, 4],
                'expected' => [4, 9],
                'name' => 'Множественные пересечения'
            ],
            [
                'nums1' => [1, 2, 3],
                'nums2' => [4, 5, 6],
                'expected' => [],
                'name' => 'Нет пересечений'
            ],
            [
                'nums1' => [],
                'nums2' => [1, 2, 3],
                'expected' => [],
                'name' => 'Первый массив пуст'
            ],
            [
                'nums1' => [1, 2, 3],
                'nums2' => [],
                'expected' => [],
                'name' => 'Второй массив пуст'
            ],
            [
                'nums1' => [1],
                'nums2' => [1],
                'expected' => [1],
                'name' => 'Один элемент'
            ],
        ];

        foreach ($tests as $test) {
            try {
                $result = $solution->intersection($test['nums1'], $test['nums2']);
                sort($result);
                sort($test['expected']);
                
                if ($result === $test['expected']) {
                    echo "✓ {$test['name']}: PASSED\n";
                    $this->passed++;
                } else {
                    echo "✗ {$test['name']}: FAILED\n";
                    echo "  Ожидалось: " . json_encode($test['expected']) . "\n";
                    echo "  Получено: " . json_encode($result) . "\n";
                    $this->failed++;
                }
            } catch (\Exception $e) {
                echo "✗ {$test['name']}: ERROR - {$e->getMessage()}\n";
                $this->failed++;
            }
        }

        echo "\n";
    }

    private function testLargestPositiveInteger(): void
    {
        echo "2. Largest Positive Integer That Exists With Its Negative\n";
        echo str_repeat("-", 70) . "\n";

        $solution = new LargestPositiveInteger();

        $tests = [
            [
                'nums' => [-1, 2, -3, 3],
                'expected' => 3,
                'name' => 'Базовый тест'
            ],
            [
                'nums' => [-1, 10, 6, 7, -7, 1],
                'expected' => 7,
                'name' => 'Несколько пар'
            ],
            [
                'nums' => [-10, 8, 6, 7, -2, -3],
                'expected' => -1,
                'name' => 'Нет пары'
            ],
            [
                'nums' => [1, -1],
                'expected' => 1,
                'name' => 'Одна пара'
            ],
            [
                'nums' => [-1],
                'expected' => -1,
                'name' => 'Только отрицательное'
            ],
            [
                'nums' => [1],
                'expected' => -1,
                'name' => 'Только положительное'
            ],
            [
                'nums' => [],
                'expected' => -1,
                'name' => 'Пустой массив'
            ],
        ];

        foreach ($tests as $test) {
            try {
                $result = $solution->findMaxK($test['nums']);
                
                if ($result === $test['expected']) {
                    echo "✓ {$test['name']}: PASSED\n";
                    $this->passed++;
                } else {
                    echo "✗ {$test['name']}: FAILED\n";
                    echo "  Ожидалось: {$test['expected']}\n";
                    echo "  Получено: {$result}\n";
                    $this->failed++;
                }
            } catch (\Exception $e) {
                echo "✗ {$test['name']}: ERROR - {$e->getMessage()}\n";
                $this->failed++;
            }
        }

        echo "\n";
    }

    private function testTwoSum(): void
    {
        echo "3. Two Sum\n";
        echo str_repeat("-", 70) . "\n";

        $solution = new TwoSum();

        $tests = [
            [
                'nums' => [2, 7, 11, 15],
                'target' => 9,
                'expected' => [0, 1],
                'name' => 'Базовый тест'
            ],
            [
                'nums' => [3, 2, 4],
                'target' => 6,
                'expected' => [1, 2],
                'name' => 'Средние индексы'
            ],
            [
                'nums' => [3, 3],
                'target' => 6,
                'expected' => [0, 1],
                'name' => 'Одинаковые числа'
            ],
            [
                'nums' => [-1, -2, -3, -4, -5],
                'target' => -8,
                'expected' => [2, 4],
                'name' => 'Отрицательные числа'
            ],
            [
                'nums' => [1, 5, 3, 7, 2],
                'target' => 9,
                'expected' => [3, 4], // nums[3] = 7, nums[4] = 2, сумма = 9
                'name' => 'Разные числа'
            ],
        ];

        foreach ($tests as $test) {
            try {
                $result = $solution->twoSum($test['nums'], $test['target']);
                sort($result);
                sort($test['expected']);
                
                if ($result === $test['expected']) {
                    echo "✓ {$test['name']}: PASSED\n";
                    $this->passed++;
                } else {
                    echo "✗ {$test['name']}: FAILED\n";
                    echo "  Ожидалось: " . json_encode($test['expected']) . "\n";
                    echo "  Получено: " . json_encode($result) . "\n";
                    $this->failed++;
                }
            } catch (\Exception $e) {
                echo "✗ {$test['name']}: ERROR - {$e->getMessage()}\n";
                $this->failed++;
            }
        }

        echo "\n";
    }

    private function testSortArrayByFrequency(): void
    {
        echo "4. Sort Array by Increasing Frequency\n";
        echo str_repeat("-", 70) . "\n";

        $solution = new SortArrayByFrequency();

        $tests = [
            [
                'nums' => [1, 1, 2, 2, 2, 3],
                'expected' => [3, 1, 1, 2, 2, 2],
                'name' => 'Базовый тест'
            ],
            [
                'nums' => [2, 3, 1, 3, 2],
                'expected' => [1, 3, 3, 2, 2],
                'name' => 'Разные частоты'
            ],
            [
                'nums' => [-1, 1, -6, 4, 5, -6, 1, 4, 1],
                'expected' => [5, -1, 4, 4, -6, -6, 1, 1, 1],
                'name' => 'Отрицательные числа'
            ],
            [
                'nums' => [1],
                'expected' => [1],
                'name' => 'Один элемент'
            ],
            [
                'nums' => [1, 2, 3, 4, 5],
                'expected' => [5, 4, 3, 2, 1],
                'name' => 'Все элементы уникальны'
            ],
            [
                'nums' => [1, 1, 1, 1],
                'expected' => [1, 1, 1, 1],
                'name' => 'Все элементы одинаковые'
            ],
            [
                'nums' => [],
                'expected' => [],
                'name' => 'Пустой массив'
            ],
        ];

        foreach ($tests as $test) {
            try {
                $result = $solution->frequencySort($test['nums']);
                
                if ($result === $test['expected']) {
                    echo "✓ {$test['name']}: PASSED\n";
                    $this->passed++;
                } else {
                    echo "✗ {$test['name']}: FAILED\n";
                    echo "  Ожидалось: " . json_encode($test['expected']) . "\n";
                    echo "  Получено: " . json_encode($result) . "\n";
                    $this->failed++;
                }
            } catch (\Exception $e) {
                echo "✗ {$test['name']}: ERROR - {$e->getMessage()}\n";
                $this->failed++;
            }
        }

        echo "\n";
    }
}

// Запуск тестов
if (php_sapi_name() === 'cli') {
    $test = new LeetCodeTest();
    $test->runAllTests();
}
