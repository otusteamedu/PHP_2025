<?php

namespace Igor\Test;

/**
 * Тесты для решения задачи Merge Two Sorted Lists
 */
class MergeTwoSortedListsTest
{
    private MergeTwoSortedLists $solution;

    public function __construct()
    {
        $this->solution = new MergeTwoSortedLists();
    }

    /**
     * Запускает все тесты
     */
    public function runAllTests(): void
    {
        echo "Запуск тестов для Merge Two Sorted Lists\n";
        echo str_repeat("=", 50) . "\n\n";

        $tests = [
            'testBasicMerge' => [$this, 'testBasicMerge'],
            'testEmptyLists' => [$this, 'testEmptyLists'],
            'testOneEmptyList' => [$this, 'testOneEmptyList'],
            'testSingleElementLists' => [$this, 'testSingleElementLists'],
            'testDifferentLengths' => [$this, 'testDifferentLengths'],
            'testAllElementsFromOneList' => [$this, 'testAllElementsFromOneList'],
            'testNegativeNumbers' => [$this, 'testNegativeNumbers'],
            'testDuplicateValues' => [$this, 'testDuplicateValues'],
        ];

        $passed = 0;
        $failed = 0;

        foreach ($tests as $testName => $testMethod) {
            try {
                $testMethod();
                echo "{$testName}: PASSED\n";
                $passed++;
            } catch (\Exception $e) {
                echo "{$testName}: FAILED - {$e->getMessage()}\n";
                $failed++;
            }
        }

        echo "\n" . str_repeat("=", 50) . "\n";
        echo "Результаты: {$passed} пройдено, {$failed} провалено\n";
    }

    /**
     * Тест 1: Базовое объединение двух списков
     */
    private function testBasicMerge(): void
    {
        $list1 = ListNode::fromArray([1, 2, 4]);
        $list2 = ListNode::fromArray([1, 3, 4]);
        $result = $this->solution->mergeTwoLists($list1, $list2);
        $expected = [1, 1, 2, 3, 4, 4];
        $actual = ListNode::toArray($result);

        if ($actual !== $expected) {
            throw new \Exception("Ожидалось: " . json_encode($expected) . ", получено: " . json_encode($actual));
        }
    }

    /**
     * Тест 2: Оба списка пусты
     */
    private function testEmptyLists(): void
    {
        $result = $this->solution->mergeTwoLists(null, null);
        if ($result !== null) {
            throw new \Exception("Ожидался null для пустых списков");
        }
    }

    /**
     * Тест 3: Один из списков пуст
     */
    private function testOneEmptyList(): void
    {
        $list1 = ListNode::fromArray([1, 2, 3]);
        $result1 = $this->solution->mergeTwoLists($list1, null);
        $expected1 = [1, 2, 3];
        $actual1 = ListNode::toArray($result1);

        if ($actual1 !== $expected1) {
            throw new \Exception("Ожидалось: " . json_encode($expected1) . ", получено: " . json_encode($actual1));
        }

        $list2 = ListNode::fromArray([4, 5, 6]);
        $result2 = $this->solution->mergeTwoLists(null, $list2);
        $expected2 = [4, 5, 6];
        $actual2 = ListNode::toArray($result2);

        if ($actual2 !== $expected2) {
            throw new \Exception("Ожидалось: " . json_encode($expected2) . ", получено: " . json_encode($actual2));
        }
    }

    /**
     * Тест 4: Списки с одним элементом
     */
    private function testSingleElementLists(): void
    {
        $list1 = ListNode::fromArray([1]);
        $list2 = ListNode::fromArray([2]);
        $result = $this->solution->mergeTwoLists($list1, $list2);
        $expected = [1, 2];
        $actual = ListNode::toArray($result);

        if ($actual !== $expected) {
            throw new \Exception("Ожидалось: " . json_encode($expected) . ", получено: " . json_encode($actual));
        }
    }

    /**
     * Тест 5: Списки разной длины
     */
    private function testDifferentLengths(): void
    {
        $list1 = ListNode::fromArray([1, 3, 5, 7, 9]);
        $list2 = ListNode::fromArray([2, 4]);
        $result = $this->solution->mergeTwoLists($list1, $list2);
        $expected = [1, 2, 3, 4, 5, 7, 9];
        $actual = ListNode::toArray($result);

        if ($actual !== $expected) {
            throw new \Exception("Ожидалось: " . json_encode($expected) . ", получено: " . json_encode($actual));
        }
    }

    /**
     * Тест 6: Все элементы из одного списка меньше элементов другого
     */
    private function testAllElementsFromOneList(): void
    {
        $list1 = ListNode::fromArray([1, 2, 3]);
        $list2 = ListNode::fromArray([4, 5, 6]);
        $result = $this->solution->mergeTwoLists($list1, $list2);
        $expected = [1, 2, 3, 4, 5, 6];
        $actual = ListNode::toArray($result);

        if ($actual !== $expected) {
            throw new \Exception("Ожидалось: " . json_encode($expected) . ", получено: " . json_encode($actual));
        }
    }

    /**
     * Тест 7: Отрицательные числа
     */
    private function testNegativeNumbers(): void
    {
        $list1 = ListNode::fromArray([-5, -3, -1]);
        $list2 = ListNode::fromArray([-4, -2, 0]);
        $result = $this->solution->mergeTwoLists($list1, $list2);
        $expected = [-5, -4, -3, -2, -1, 0];
        $actual = ListNode::toArray($result);

        if ($actual !== $expected) {
            throw new \Exception("Ожидалось: " . json_encode($expected) . ", получено: " . json_encode($actual));
        }
    }

    /**
     * Тест 8: Дублирующиеся значения
     */
    private function testDuplicateValues(): void
    {
        $list1 = ListNode::fromArray([1, 1, 2, 3]);
        $list2 = ListNode::fromArray([1, 2, 2, 4]);
        $result = $this->solution->mergeTwoLists($list1, $list2);
        $expected = [1, 1, 1, 2, 2, 2, 3, 4];
        $actual = ListNode::toArray($result);

        if ($actual !== $expected) {
            throw new \Exception("Ожидалось: " . json_encode($expected) . ", получено: " . json_encode($actual));
        }
    }
}
