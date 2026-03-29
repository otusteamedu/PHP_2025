<?php

declare(strict_types=1);

namespace App\Domain\Product\Strategy;

use App\Domain\Entities\Product;
use App\Domain\Interfaces\ProductInterface;
use App\Domain\Interfaces\ProductStrategyInterface;

class BurgerStrategy implements ProductStrategyInterface
{
    public function createProduct(): ProductInterface
    {
        return new Product(
            type: 'burger',
            name: 'Бургер',
            description: 'Классический бургер с котлетой из говядины',
            price: 250.00,
            ingredients: ['булочка', 'котлета из говядины', 'кетчуп', 'горчица']
        );
    }

    public function getType(): string
    {
        return 'burger';
    }
}
