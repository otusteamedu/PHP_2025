<?php

declare(strict_types=1);

namespace App\Domain\Cooking;

use App\Domain\Cooking\Exception\ProductIsBadException;
use App\Domain\Product\ProductInterface;

class BurgerCooker implements ProductCookerInterface
{
    public function preCook(ProductInterface $product): void
    {
        echo sprintf("Проверяем ингредиенты для продукта - %s\n", $product->getName());
    }

    public function cook(ProductInterface $product): void
    {
        echo sprintf("Готовим %s\n", $product->getName());
    }

    public function postCook(ProductInterface $product): void
    {
        $productName = $product->getName();
        echo sprintf("Проверяем качество продукта - %s\n", $productName);

        $isGood = rand(0, 1);

        if (!$isGood) {
            throw new ProductIsBadException($productName);
        }
    }
}
