<?php

declare(strict_types=1);

namespace Otus\Food\Tests\Application\Builder;

use Otus\Food\Application\Builder\AbstractBuilder;
use Otus\Food\Application\Builder\Proxy;
use Otus\Food\Application\Observer\Kitchen;
use Otus\Food\Domain\Kitchen\Entity\Meal;
use Otus\Food\Domain\Order\Order;
use PHPUnit\Framework\TestCase;

class ProxyTest extends TestCase
{
    public function testProxy(): void
    {
        $meal = $this->createStub(Meal::class);
        $order = $this->createStub(Order::class);

        $builder = $this->createMock(AbstractBuilder::class);
        $builder->method('getMeal')->willReturn($meal);
        $builder->method('getOrder')->willReturn($order);
        $builder->expects($this->once())->method('cooking')->willReturn($meal);

        $kitchen = new Kitchen();

        $proxy = new Proxy($builder, $kitchen);
        $result = $proxy->cooking();

        $this->assertSame($meal, $result);
        $this->assertSame($builder, $kitchen->getBegin());
        $this->assertSame($builder, $kitchen->getEnd());
    }
}
