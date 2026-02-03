<?php

declare(strict_types=1);

namespace App\Domain\Ingredient\Factory;

use App\Domain\Ingredient\CheeseDecorator;
use App\Domain\Ingredient\JalapenoDecorator;
use App\Domain\Ingredient\OnionDecorator;
use App\Domain\Ingredient\TomatoDecorator;
use App\Domain\Product\ProductInterface;

class IngredientDecoratorFactory
{
    public function addIngredient(ProductInterface $product, array $ingredients): ProductInterface
    {
        foreach ($ingredients as $ingredient) {
            $product = match ($ingredient) {
                'Сыр' => new CheeseDecorator($product),
                'Лук' => new OnionDecorator($product),
                'Халапеньо' => new JalapenoDecorator($product),
                'Томаты' => new TomatoDecorator($product),
                default => $product,
            };
        }

        return $product;
    }
}
