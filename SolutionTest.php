<?php

declare(strict_types=1);

require_once __DIR__ . '/Solution.php';

use PHPUnit\Framework\TestCase;

final class SolutionTest extends TestCase
{
    public function testTwoSum(): void
    {
        $this->assertEquals([0, 1], Solution::twoSum([2, 7, 11, 15], 9));
        $this->assertEquals([1, 2], Solution::twoSum([3, 2, 4], 6));
        $this->assertEquals([0, 1], Solution::twoSum([3, 3], 6));
        $this->assertEquals([0, 1], Solution::twoSum([2,7,11,15], 9));
    }

    public function testFrequencySort(): void
    {
        $this->assertEquals([3, 1, 1, 2, 2, 2], Solution::frequencySort([1, 1, 2, 2, 2, 3]));
        $this->assertEquals([1, 3, 3, 2, 2], Solution::frequencySort([2, 3, 1, 3, 2]));
        $this->assertEquals([5, -1, 4, 4, -6, -6, 1, 1, 1], Solution::frequencySort([1, 1, 1, -1, -6, -6, 4, 4, 5]));
        $this->assertEquals([7, 7, 7, 7], Solution::frequencySort([7, 7, 7, 7]));
        $this->assertEquals([], Solution::frequencySort([]));
    }

    public function testIntersection(): void
    {
        $this->assertEquals([2], Solution::intersection([1, 2, 2, 1], [2, 2]));
        $this->assertEquals([4, 9], Solution::intersection([4, 9, 5], [9, 4, 9, 8, 4]));
        $this->assertEquals([], Solution::intersection([1, 2, 3], [4, 5, 6]));
        $this->assertEquals([-1], Solution::intersection([-1, -2, -3], [-1, -5, -6]));
        $this->assertEquals([], Solution::intersection([], [1, 2, 3]));
        $this->assertEquals([], Solution::intersection([1, 2, 3], []));
        $this->assertEquals([], Solution::intersection([], []));
    }

    public function testFindMaxK(): void
    {
        $this->assertEquals(3, Solution::findMaxK([-1, 2, -3, 3]));
        $this->assertEquals(7, Solution::findMaxK([-1, 10, 6, 7, -7, 1]));
        $this->assertEquals(-1, Solution::findMaxK([-10, 8, 6, 7, -2]));
        $this->assertEquals(-1, Solution::findMaxK([-1, -2, -3]));
        $this->assertEquals(-1, Solution::findMaxK([1, 2, 3]));
        $this->assertEquals(1000, Solution::findMaxK([-1000, 1000, -500, 500]));
        $this->assertEquals(-1, Solution::findMaxK([]));
    }

    public function testSmallerNumbersThanCurrent(): void
    {
        $this->assertEquals([4, 0, 1, 1, 3], Solution::smallerNumbersThanCurrent([8, 1, 2, 2, 3]));
        $this->assertEquals([2, 1, 0, 3], Solution::smallerNumbersThanCurrent([6, 5, 4, 8]));
        $this->assertEquals([0, 0, 0, 0], Solution::smallerNumbersThanCurrent([7, 7, 7, 7]));
        $this->assertEquals([2, 0, 1], Solution::smallerNumbersThanCurrent([-1, -3, -2]));
        $this->assertEquals([], Solution::smallerNumbersThanCurrent([]));
    }
}