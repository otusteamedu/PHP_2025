<?php

declare(strict_types=1);

namespace App\Domain\Product\Strategy;

use App\Domain\Entities\Product;
use App\Domain\Interfaces\ProductInterface;
use App\Domain\Interfaces\ProductStrategyInterface;

class HotDogStrategy implements ProductStrategyInterface
{
    public function createProduct(): ProductInterface
    {
        return new Product(
            name: 'Хот-дог',
            description: 'Классический хот-дог с сосиской',
            price: 150.00,
            ingredients: ['булочка для хот-дога', 'сосиска', 'кетчуп', 'горчица']
        );
    }

    public function getType(): string
    {
        return 'hotdog';
    }
}
