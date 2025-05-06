<?php

declare(strict_types=1);

namespace App\Core\Decorator;

use App\Core\Factories\FastFoodFactory;

class SandwichDecorator extends ProductDecorator
{
    public function __construct(FastFoodFactory $product, array $ingredients)
    {
        parent::__construct($product, $ingredients);
        $this->setName();
    }

    /**
     * @return void
     */
    public function setName(): void
    {
        $this->name = $this->product->getName();
        if (!empty($this->ingredients)) {
            foreach ($this->ingredients as $ingredient) {
                if ($ingredient === 'salad') {
                    $this->name .= " with salad";
                }
                if ($ingredient === 'tomatoes') {
                    $this->name .= " with tomatoes";
                }
                if ($ingredient === 'mustard') {
                    $this->name .= " with mustard";
                }
            }
        }
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        $this->price = $this->product->getPrice();
        if (!empty($this->ingredients)) {
            foreach ($this->ingredients as $ingredient) {
                if ($ingredient === 'salad') {
                    $this->price += 30;
                }
                if ($ingredient === 'tomatoes') {
                    $this->price += 30;
                }
                if ($ingredient === 'mustard') {
                    $this->price += 10;
                }
            }
        }

        return $this->price;
    }
}