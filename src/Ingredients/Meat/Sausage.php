<?php

namespace Shop\Ingredients\Meat;

class Sausage implements BaseMeat
{

    public function getNutritionalInfo(): array
    {
        return [
            'calories' => 200,
            'fat' => 6,
            'fiber' => 2,
            'protein' => 20
        ];
    }

    public function getType(): string
    {
        return 'Курица';
    }

    public function getName(): string
    {
        return 'Сосиска';
    }

    public function getPrice(): float
    {
        return 200;
    }
}