<?php

declare(strict_types=1);

namespace App\Core\Decorator;

use App\Core\Factories\FastFoodFactory;

class BurgerDecorator extends ProductDecorator
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
                if ($ingredient === 'cheese') {
                    $this->name .= " with cheese";
                }
                if ($ingredient === 'onion') {
                    $this->name .= " with onion";
                }
                if ($ingredient === 'bacon') {
                    $this->name .= " with bacon";
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
                if ($ingredient === 'cheese') {
                    $this->price += 50;
                }
                if ($ingredient === 'onion') {
                    $this->price += 20;
                }
                if ($ingredient === 'bacon') {
                    $this->price += 80;
                }
            }
        }

        return $this->price;
    }
}