<?php

declare(strict_types=1);

namespace Otus\Queue\Tests\Infrastructure\Component;

use Otus\Queue\Infrastructure\Component\Collection;
use PHPUnit\Framework\TestCase;

final class CollectionTest extends TestCase
{
    public function testBasicGetSetAndPullFlow(): void
    {
        $collection = Collection::make(['a' => 1]);

        $collection->set('b', 2);

        self::assertSame(2, $collection->get('b'));
        self::assertTrue($collection->has('a'));
        self::assertSame(1, $collection->pull('a'));
        self::assertFalse($collection->has('a'));
    }

    public function testMapFilterAndReduceKeepExpectedValues(): void
    {
        $collection = Collection::make(['x' => 1, 'y' => 2, 'z' => 3]);

        $mapped = $collection->map(static fn (int $value): int => $value * 10);
        $filtered = $mapped->filter(static fn (int $value): bool => $value >= 20);
        $sum = $filtered->reduce(static fn (int $carry, int $value): int => $carry + $value, 0);

        self::assertSame(['x' => 10, 'y' => 20, 'z' => 30], $mapped->all());
        self::assertSame(['y' => 20, 'z' => 30], $filtered->all());
        self::assertSame(50, $sum);
    }

    public function testFirstAndLastReturnNullOnEmptyCollection(): void
    {
        $collection = Collection::make();

        self::assertNull($collection->first());
        self::assertNull($collection->last());
        self::assertTrue($collection->isEmpty());
    }

    public function testUtilityMethodsWrapPushContainsKeysValuesEachMergeAndCount(): void
    {
        $collection = Collection::make(['a' => 1]);
        $collection->push(2);

        self::assertTrue($collection->contains(2));
        self::assertSame(['a', 0], $collection->keys());
        self::assertSame([1, 2], $collection->values());
        self::assertSame(2, $collection->count());

        $wrapped = Collection::make(['list' => ['x' => 10]])->wrap('list');
        self::assertSame(['x' => 10], $wrapped->all());

        $sum = 0;
        $collection->each(static function (int $value) use (&$sum): void {
            $sum += $value;
        });
        self::assertSame(3, $sum);

        $merged = $collection->merge(['b' => 3]);
        self::assertSame(['a' => 1, 0 => 2, 'b' => 3], $merged->all());

        $collection->remove('a');
        self::assertFalse($collection->has('a'));
        self::assertSame(2, $collection->first());
        self::assertSame(2, $collection->last());
    }
}
