<?php

declare(strict_types=1);

namespace App\Domain\Product\Factory;

use App\Domain\Product\Burger;
use App\Domain\Product\Exception\InvalidProductException;
use App\Domain\Product\HotDog;
use App\Domain\Product\ProductInterface;
use App\Domain\Product\Sandwich;

class ProductFactory
{
    private const float BURGER_PRICE = 3.99;
    private const float SANDWICH_PRICE = 2.49;
    private const float HOT_DOG_PRICE = 2.99;

    public function create(string $name): ProductInterface
    {
        return match ($name) {
            'Бургер' => new Burger($name, self::BURGER_PRICE),
            'Сэндвич' => new Sandwich($name, self::SANDWICH_PRICE),
            'Хот-дог' => new HotDog($name, self::HOT_DOG_PRICE),
            default => throw new InvalidProductException($name),
        };
    }
}
