<?php

namespace App\Factory;

use App\Model\Product;
use App\Builder\ProductBuilder;

class ProductFactory 
{
    public function __construct(private ProductBuilder $builder) {}

    public function create(string $type, array $additives = []): Product 
    {
        $product = $this->builder->build($type);

        $oldProductName = 'product';
        foreach ($additives as $additive) {
            $decoratorClass = 'App\\Decorator\\' . ucfirst($additive) . 'Decorator';
            if (class_exists($decoratorClass)) {
                $newProductName = $additive . ucfirst($oldProductName);
                $$newProductName = new $decoratorClass($$oldProductName);
                $oldProductName = $newProductName;
            }
        }

        return $$oldProductName;
    }
}