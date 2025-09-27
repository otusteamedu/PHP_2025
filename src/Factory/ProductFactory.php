<?php

namespace App\Factory;

use App\Model\Product;
use App\Builder\ProductBuilder;
use App\Decorator\ProductDecorator;
use InvalidArgumentException;

class ProductFactory 
{
    private const DECORATOR_NAMESPACE = 'App\\Decorator\\';
    private const DECORATOR_SUFFIX = 'Decorator';

    public function __construct(private ProductBuilder $builder) {}

    public function create(string $type, array $additives = []): Product 
    {
        $product = $this->builder->build($type);
        
        foreach ($additives as $additive) {
            $product = $this->applyDecorator($product, $additive);
        }

        return $product;
    }

    private function applyDecorator(Product $product, string $additive): Product
    {
        $decoratorClass = $this->getDecoratorClassName($additive);
        
        if (!class_exists($decoratorClass)) {
            throw new InvalidArgumentException("Декоратор $additive не найден");
        }

        if (!is_subclass_of($decoratorClass, ProductDecorator::class)) {
            throw new InvalidArgumentException("Класс $decoratorClass не является декоратором");
        }

        return new $decoratorClass($product);
    }

    private function getDecoratorClassName(string $additive): string
    {
        return self::DECORATOR_NAMESPACE . ucfirst($additive) . self::DECORATOR_SUFFIX;
    }
}
