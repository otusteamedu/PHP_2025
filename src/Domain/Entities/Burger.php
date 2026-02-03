<?php

namespace App\Domain\Entities;

use App\Domain\Enums\ProductType;

class Burger extends Product
{
    public function __construct(string $name = 'Классический Бургер')
    {
        parent::__construct($name, ProductType::BURGER, 500.00); // 500 рублей
        $this->ingredients = ['Булочка', 'Говяжья котлета'];
    }

    public function getPrice(): float
    {
        return $this->basePrice + (count($this->ingredients) * 50); // +50 руб за каждый ингредиент
    }
}