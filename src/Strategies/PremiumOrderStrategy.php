<?php

declare(strict_types=1);

namespace App\Strategies;

use App\Products\ProductInterface;

class PremiumOrderStrategy implements OrderStrategyInterface
{
    public function calculateTotal(array $products): int
    {
        return array_reduce($products, function(int $total, ProductInterface $product): int {
            return $total + $product->price;
        }, 0);
    }

    public function applyDiscount(int $total): int
    {
        if ($total >= 1000) {
            return (int)($total * 0.85);
        }
        return $total;
    }

    public function getOrderDescription(array $products): string
    {
        $productNames = array_map(fn(ProductInterface $product) => $product->name, $products);
        return 'Премиум заказ: ' . implode(', ', $productNames) . ' (со скидкой 15%)';
    }
}
