<?php

declare(strict_types=1);

namespace App\Core\Decorator;

use App\Core\Factories\FastFoodFactory;

class HotDogDecorator extends ProductDecorator
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
                if ($ingredient === 'cucumbers') {
                    $this->name .= " with cucumbers";
                }
                if ($ingredient === 'onion') {
                    $this->name .= " with onion";
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
                if ($ingredient === 'cucumbers') {
                    $this->price += 30;
                }
                if ($ingredient === 'onion') {
                    $this->price += 20;
                }
                if ($ingredient === 'mustard') {
                    $this->price += 10;
                }
            }
        }

        return $this->price;
    }
}