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
        foreach ($additives as $additive) {
            $decoratorClass = 'App\\Decorator\\' . ucfirst($additive) . 'Decorator';
            if (class_exists($decoratorClass)) {
                $product = new $decoratorClass($product);
            }
        }
        return $product;
    }
}