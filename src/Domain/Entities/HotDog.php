<?php

namespace App\Domain\Entities;

use App\Domain\Enums\ProductType;

class HotDog extends Product
{
    public function __construct(string $name = 'Классический Хот-Дог')
    {
        parent::__construct($name, ProductType::HOTDOG, 300.00); // 300 рублей
        $this->ingredients = ['Булочка', 'Сосиска'];
    }

    public function getPrice(): float
    {
        return $this->basePrice + (count($this->ingredients) * 30);
    }
}