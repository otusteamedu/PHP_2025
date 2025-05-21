<?php

namespace Tests\Unit\Domain\Factories;

use Domain\Factories\FastFoodFactory;
use Domain\Products\Burger;
use Domain\Products\HotDog;
use Domain\Products\Sandwich;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class FastFoodFactoryTest extends TestCase
{
    public function testCreateBurger()
    {
        $product = FastFoodFactory::createProduct('Burger');
        $this->assertInstanceOf(Burger::class, $product);
    }

    public function testCreateHotDog()
    {
        $product = FastFoodFactory::createProduct('HotDog');
        $this->assertInstanceOf(HotDog::class, $product);
    }

    public function testCreateSandwich()
    {
        $product = FastFoodFactory::createProduct('Sandwich');
        $this->assertInstanceOf(Sandwich::class, $product);
    }

    public function testInvalidProduct()
    {
        $this->expectException(InvalidArgumentException::class);
        FastFoodFactory::createProduct('Pizza');
    }
}

