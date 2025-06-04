<?php

declare(strict_types=1);

namespace Domain\Factories;

use Domain\Products\Burger;
use Domain\Products\HotDog;
use Domain\Products\Product;
use Domain\Products\Sandwich;
use InvalidArgumentException;

class FastFoodFactory implements FastFoodFactoryInterface
{
    public static function  createProduct(string $name): Product
    {
        return match ($name) {
            'Burger' => new Burger(),
            'Sandwich' => new Sandwich(),
            'HotDog' => new HotDog(),
            default => throw new InvalidArgumentException("Неизвестный продукт: $name")
        };
    }
}
