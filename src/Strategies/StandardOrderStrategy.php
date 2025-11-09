<?php

declare(strict_types=1);

namespace App\Strategies;

use App\Products\ProductInterface;

class StandardOrderStrategy implements OrderStrategyInterface
{
    public function calculateTotal(array $products): int
    {
        return array_reduce($products, function(int $total, ProductInterface $product): int {
            return $total + $product->price;
        }, 0);
    }

    public function applyDiscount(int $total): int
    {
        return $total;
    }

    public function getOrderDescription(array $products): string
    {
        $productNames = array_map(fn(ProductInterface $product) => $product->name, $products);
        return 'Стандартный заказ: ' . implode(', ', $productNames);
    }
}
