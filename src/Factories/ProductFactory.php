<?php

namespace App\Factories;

use App\Products\BaseProduct;
use App\Strategies\BurgerStrategy;
use App\Strategies\SandwichStrategy;
use App\Strategies\HotDogStrategy;
use App\Strategies\ProductStrategyInterface;

class ProductFactory implements ProductFactoryInterface
{
    private array $strategies;

    public function __construct()
    {
        $this->strategies = [
            'burger' => new BurgerStrategy(),
            'sandwich' => new SandwichStrategy(),
            'hotdog' => new HotDogStrategy(),
        ];
    }

    public function createProduct(string $type): BaseProduct
    {
        if (!isset($this->strategies[$type])) {
            throw new \InvalidArgumentException("Unknown product type: {$type}");
        }

        $strategy = $this->strategies[$type];
        return $strategy->createBaseProduct();
    }
}