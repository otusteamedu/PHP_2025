<?php

namespace App\Infrastructure\Strategies;

use App\Domain\Interfaces\ProductPrototypeStrategyInterface;
use App\Domain\Enums\ProductType;

class ProductPrototypeFactory
{
    /** @var array<string, ProductPrototypeStrategyInterface> */
    private array $strategies = [];

    public function __construct(
        BurgerPrototypeStrategy $burgerStrategy,
        SandwichPrototypeStrategy $sandwichStrategy,
        HotDogPrototypeStrategy $hotDogStrategy
    ) {
        $this->strategies = [
            ProductType::BURGER->value => $burgerStrategy,
            ProductType::SANDWICH->value => $sandwichStrategy,
            ProductType::HOTDOG->value => $hotDogStrategy,
        ];
    }

    public function getStrategy(ProductType $type): ProductPrototypeStrategyInterface
    {
        return $this->strategies[$type->value] 
            ?? throw new \InvalidArgumentException("No strategy found for type: {$type->value}");
    }
}