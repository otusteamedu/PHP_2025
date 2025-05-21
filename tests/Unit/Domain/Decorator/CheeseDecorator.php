<?php

namespace Tests\Unit\Domain\Decorator;

use Domain\Decorator\CheeseDecorator;
use Domain\Products\Burger;
use PHPUnit\Framework\TestCase;

class CheeseDecoratorTest extends TestCase
{
    public function testGetName()
    {
        $burger = new Burger();
        $decorated = new CheeseDecorator($burger);
        $this->assertEquals('Burger, with cheese', $decorated->getName());
    }

    public function testGetPrice()
    {
        $burger = new Burger();
        $decorated = new CheeseDecorator($burger);
        $this->assertEquals(400, $decorated->getPrice());
    }
}