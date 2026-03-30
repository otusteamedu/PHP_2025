<?php

namespace App\Factories;

use App\Products\ProductInterface;
use App\Strategies\BurgerStrategy;
use App\Strategies\SandwichStrategy;
use App\Strategies\HotDogStrategy;
use App\Strategies\PizzaStrategy;

class ProductFactory implements ProductFactoryInterface
{
    private array $strategies;

    public function __construct()
    {
        $this->strategies = [
            'burger' => new BurgerStrategy(),
            'sandwich' => new SandwichStrategy(),
            'hotdog' => new HotDogStrategy(),
            'pizza' => new PizzaStrategy(),
        ];
    }

    public function createProduct(string $type): ProductInterface
    {
        if (!isset($this->strategies[$type])) {
            throw new \InvalidArgumentException("Unknown product type: {$type}");
        }

        $strategy = $this->strategies[$type];
        return $strategy->createBaseProduct();
    }
}
