<?php

declare(strict_types=1);

namespace UnitTests\hw18\task_1;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../../algo/hw18/task_1/IntersectionOfTwoLinkedLists.php';
require_once __DIR__ . '/../../../../algo/hw18/task_1/ListNode.php';

class IntersectionOfTwoLinkedListsTest extends TestCase
{
    private \IntersectionOfTwoLinkedLists $finder;

    protected function setUp(): void
    {
        $this->finder = new \IntersectionOfTwoLinkedLists();
    }

    #[DataProvider('provideIntersectingLists')]
    public function testReturnsIntersectionNodeWhenListsIntersect(
        \ListNode $headA,
        \ListNode $headB,
        \ListNode $expected,
        int $expectedVal,
    ): void {
        $result = $this->finder->getIntersectionNode($headA, $headB);

        $this->assertSame($expected, $result);
        $this->assertEquals($expectedVal, $result?->val);
    }

    public function testReturnsNullWhenThereIsNoIntersection(): void
    {
        $headA = new \ListNode(2, new \ListNode(6, new \ListNode(4)));
        $headB = new \ListNode(1, new \ListNode(5));

        $result = $this->finder->getIntersectionNode($headA, $headB);

        $this->assertNull($result);
    }

    /**
     * @return array<string, array{\ListNode, \ListNode, \ListNode, int}>
     */
    public static function provideIntersectingLists(): array
    {
        $common1 = new \ListNode(8, new \ListNode(4, new \ListNode(5)));
        $headA1 = new \ListNode(4, new \ListNode(1, $common1));
        $headB1 = new \ListNode(5, new \ListNode(6, new \ListNode(1, $common1)));

        $common2 = new \ListNode(2, new \ListNode(4));
        $headA2 = new \ListNode(1, new \ListNode(9, new \ListNode(1, $common2)));
        $headB2 = new \ListNode(3, $common2);

        return [
            'intersection after several nodes in both lists' => [$headA1, $headB1, $common1, 8],
            'intersection near head of one list' => [$headA2, $headB2, $common2, 2],
        ];
    }
}
