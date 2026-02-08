<?php

declare(strict_types=1);

namespace Otus\Food\Tests\Application\Strategy;

use Otus\Food\Application\Strategy\Burger;
use Otus\Food\Application\Strategy\Harvester;
use Otus\Food\Application\Strategy\HarvesterException;
use Otus\Food\Application\Strategy\MealStrategy;
use Otus\Food\Application\Strategy\Pizza\Chef;
use Otus\Food\Application\Strategy\Sandwich;
use Otus\Food\Domain\Order\Buyer;
use Otus\Food\Domain\Order\Order;
use PHPUnit\Framework\TestCase;

class StrategyTest extends TestCase
{
    public function testHarvester(): void
    {
        $strategy = $this->createStub(MealStrategy::class);
        $harvester = new Harvester(['test' => $strategy]);

        $this->assertSame($strategy, $harvester->getStrategy('test'));

        $this->expectException(HarvesterException::class);
        $harvester->getStrategy('unknown');
    }

    public function testBurgerStrategy(): void
    {
        $order = new Order(new Buyer('John'));
        $strategy = new Burger();
        $meal = $strategy->cooking($order);
        $this->assertSame('Burger', $meal->getTitle());
    }

    public function testSandwichStrategy(): void
    {
        $order = new Order(new Buyer('John'));
        $strategy = new Sandwich();
        $meal = $strategy->cooking($order);
        $this->assertSame('Sandwich', $meal->getTitle());
    }

    public function testPizzaChefStrategy(): void
    {
        $order = new Order(new Buyer('John'));
        $strategy = new Chef();
        $meal = $strategy->cooking($order);
        $this->assertSame('Pizza', $meal->getTitle());
    }
}
