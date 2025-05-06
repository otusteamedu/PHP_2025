<?php

declare(strict_types=1);

namespace App\Core\Factories;

class FastFoodFactory implements FastFoodFactoryInterface
{
    public string $name;
    public float $price;

    public function __construct(array $productData)
    {
        $this->name = $productData['name'];
        $this->price = $productData['price'];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }
}