<?php

namespace App; 

use App\Factories\ProductFactory;
use App\Decorators\ProductDecoratorInterface;

class Order
{
    private Kitchen $kitchen;
    private ProductFactory $factory;

    public function __construct(Kitchen $kitchen, ProductFactory $factory)
    {
        $this->kitchen = $kitchen;
        $this->factory = $factory;
    }

    public function createOrder(string $productType, array $decorators = []): void
    {
        // Фабричный метод создает продукт
        $product = $this->factory->createProduct($productType);
        
        $this->kitchen->setProduct($product);
        $this->kitchen->changeStatus('preparing');

        // Применяем декораторы
        foreach ($decorators as $decorator) {
            if ($decorator instanceof ProductDecoratorInterface) {
                $this->kitchen->applyDecorator($decorator);
            }
        }

        $this->kitchen->changeStatus('ready');
    }
}