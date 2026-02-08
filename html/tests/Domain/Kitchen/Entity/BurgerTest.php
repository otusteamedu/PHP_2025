<?php

declare(strict_types=1);

namespace Otus\Food\Tests\Domain\Kitchen\Entity;

use Otus\Food\Domain\Kitchen\Entity\Burger;
use PHPUnit\Framework\TestCase;

class BurgerTest extends TestCase
{
    public function testGetTitle(): void
    {
        $burger = new Burger();
        $this->assertSame('Burger', $burger->getTitle());
    }

    public function testGetRecept(): void
    {
        $burger = new Burger();
        $this->assertSame(['bun'], $burger->getRecept());
    }

    public function testDecorators(): void
    {
        $burger = new Burger();
        $burger = $burger->beef()
            ->cheese()
            ->chicken()
            ->cucumber()
            ->ketchup()
            ->lactuca()
            ->mayonnaise()
            ->mushrooms()
            ->olive()
            ->sauce()
            ->tomato();

        $expectedRecept = [
            'bun',
            'beef',
            'cheese',
            'chicken',
            'cucumber',
            'ketchup',
            'lactuca',
            'mayonnaise',
            'mushrooms',
            'olive',
            'sauce',
            'tomato',
        ];

        $this->assertSame($expectedRecept, $burger->getRecept());
        $this->assertSame('Burger', $burger->getTitle());
    }

    public function testClone(): void
    {
        $burger = new Burger();
        $cloned = $burger->clone();

        $this->assertNotSame($burger, $cloned);
        $this->assertEquals($burger, $cloned);
    }
}
