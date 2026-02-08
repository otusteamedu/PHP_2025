<?php

namespace Shop\Ingredients\Meat;

class Cutlet implements BaseMeat
{

    public function getType(): string
    {
        return 'Курица';
    }

    public function getName(): string
    {
        return 'Котлета';
    }

    public function getNutritionalInfo(): array
    {
        return [
            'calories' => 100,
            'fat' => 5,
            'fiber' => 2,
            'protein' => 20
        ];
    }

    public function getPrice(): float
    {
        return 1001;
    }
}