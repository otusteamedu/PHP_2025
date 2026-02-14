<?php

declare(strict_types=1);

namespace App\Domain\Product\Strategy;

use App\Domain\Entities\Product;
use App\Domain\Interfaces\ProductInterface;
use App\Domain\Interfaces\ProductStrategyInterface;

class SandwichStrategy implements ProductStrategyInterface
{
    public function createProduct(): ProductInterface
    {
        return new Product(
            name: 'Сэндвич',
            description: 'Сэндвич с курицей и овощами',
            price: 200.00,
            ingredients: ['хлеб', 'курица', 'майонез']
        );
    }

    public function getType(): string
    {
        return 'sandwich';
    }
}
