<?php

declare(strict_types=1);

namespace Otus\Food\Tests\Application\Observer;

use Otus\Food\Application\Builder\AbstractBuilder;
use Otus\Food\Application\Observer\Kitchen;
use Otus\Food\Application\Observer\Monitor;
use Otus\Food\Domain\Kitchen\Entity\Meal;
use Otus\Food\Domain\Order\Buyer;
use Otus\Food\Domain\Order\Order;
use PHPUnit\Framework\TestCase;
use SplObserver;

class KitchenObserverTest extends TestCase
{
    private const string SOCKET_FILE = 'monitor.sock';

    protected function tearDown(): void
    {
        if (file_exists(self::SOCKET_FILE)) {
            unlink(self::SOCKET_FILE);
        }
    }

    public function testKitchenObserver(): void
    {
        $kitchen = new Kitchen();
        $monitor = new Monitor();

        $kitchen->attach($monitor);

        $meal = $this->createStub(Meal::class);
        $meal->method('getTitle')->willReturn('TestMeal');

        $order = new Order(new Buyer('TestBuyer'));

        $builder = $this->createStub(AbstractBuilder::class);
        $builder->method('getMeal')->willReturn($meal);
        $builder->method('getOrder')->willReturn($order);

        $kitchen->begin($builder)->notify();
        $this->assertSame($builder, $kitchen->getBegin());
        $this->assertFileExists(self::SOCKET_FILE);
        $content = file_get_contents(self::SOCKET_FILE);
        $this->assertStringContainsString('Begin', $content);
        $this->assertStringContainsString('TestMeal', $content);
        $this->assertStringContainsString('TestBuyer', $content);

        $kitchen->end($builder)->notify();
        $this->assertSame($builder, $kitchen->getEnd());
        $content = file_get_contents(self::SOCKET_FILE);
        $this->assertStringContainsString('End', $content);
    }

    public function testDetach(): void
    {
        $kitchen = new Kitchen();
        $observer = $this->createMock(SplObserver::class);
        $observer->expects($this->never())->method('update');

        $kitchen->attach($observer);
    }
}
