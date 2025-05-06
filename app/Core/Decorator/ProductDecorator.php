<?php

declare(strict_types=1);

namespace App\Core\Decorator;

use App\Core\Factories\FastFoodFactory;

abstract class ProductDecorator implements ProductInterface
{
    protected FastFoodFactory $product;
    protected array $ingredients = [];
    protected string $name;

    protected float $price;

    public function __construct(FastFoodFactory $product, array $ingredients)
    {
        $this->product = $product;
        $this->ingredients = $ingredients;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}