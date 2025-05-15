<?php

declare(strict_types=1);

namespace Domain\Strategy;

use Domain\Decorator\BaconDecorator;
use Domain\Decorator\CheeseDecorator;
use Domain\Decorator\CucumbersDecorator;
use Domain\Decorator\MustardDecorator;
use Domain\Decorator\OnionDecorator;
use Domain\Decorator\SaladDecorator;
use Domain\Decorator\TomatoesDecorator;
use Domain\Products\Product;
use InvalidArgumentException;

readonly class ProductStrategy implements CookingStrategyInterface
{
    public function __construct(
        private Product $product,
        private array $ingredients = []
    )
    {}

    public function cook(): Product
    {
        $decoratedProduct = $this->product;

        foreach ($this->ingredients as $ingredient) {
            $decoratedProduct = match($ingredient) {
                'cheese' => new CheeseDecorator($decoratedProduct),
                'onion' => new OnionDecorator($decoratedProduct),
                'bacon' => new BaconDecorator($decoratedProduct),
                'mustard' => new MustardDecorator($decoratedProduct),
                'cucumbers' => new CucumbersDecorator($decoratedProduct),
                'salad' => new SaladDecorator($decoratedProduct),
                'tomatoes' => new TomatoesDecorator($decoratedProduct),
                default => throw new InvalidArgumentException("Неизвестный ингредиент: $ingredient")
            };
        }

        return $decoratedProduct;
    }
}