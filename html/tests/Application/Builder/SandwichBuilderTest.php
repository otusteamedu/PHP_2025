<?php

declare(strict_types=1);

namespace Otus\Food\Tests\Application\Builder;

use Otus\Food\Application\Builder\Sandwich as SandwichBuilder;
use Otus\Food\Domain\Kitchen\Entity\Sandwich as SandwichEntity;
use Otus\Food\Domain\Order\Buyer;
use Otus\Food\Domain\Order\Order;
use PHPUnit\Framework\TestCase;

class SandwichBuilderTest extends TestCase
{
    public function testCooking(): void
    {
        $meal = new SandwichEntity();
        $buyer = new Buyer('John Doe');
        $order = new Order($buyer);

        $builder = new SandwichBuilder($meal, $order);
        $cookedMeal = $builder->cooking();

        $this->assertInstanceOf(SandwichEntity::class, $builder->getMeal());
        $this->assertSame($order, $builder->getOrder());

        $expectedRecept = [
            'toaster bread',
            'chicken',
            'lactuca',
            'tomato',
            'cucumber',
            'mayonnaise',
        ];

        $this->assertSame($expectedRecept, $cookedMeal->getRecept());
        $this->assertSame('Sandwich', $cookedMeal->getTitle());
    }
}
