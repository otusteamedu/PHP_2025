<?php

declare(strict_types=1);

namespace Otus\Food\Tests\Application\Factory;

use Otus\Food\Application\Factory\BurgerFactory;
use Otus\Food\Application\Factory\PizzaFactory;
use Otus\Food\Application\Factory\SandwichFactory;
use Otus\Food\Domain\Kitchen\Entity\Burger;
use Otus\Food\Domain\Kitchen\Entity\Pizza;
use Otus\Food\Domain\Kitchen\Entity\Sandwich;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    public function testBurgerFactory(): void
    {
        $this->assertInstanceOf(Burger::class, BurgerFactory::factory());
    }

    public function testPizzaFactory(): void
    {
        $this->assertInstanceOf(Pizza::class, PizzaFactory::factory());
    }

    public function testSandwichFactory(): void
    {
        $this->assertInstanceOf(Sandwich::class, SandwichFactory::factory());
    }
}
