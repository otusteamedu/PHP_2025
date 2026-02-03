<?php

namespace App\Domain\Entities;

use App\Domain\Enums\ProductType;

class Sandwich extends Product
{
    public function __construct(string $name = 'Классический Сэндвич')
    {
        parent::__construct($name, ProductType::SANDWICH, 400.00); // 400 рублей
        $this->ingredients = ['Хлеб', 'Сыр'];
    }

    public function getPrice(): float
    {
        return $this->basePrice + (count($this->ingredients) * 40);
    }
}