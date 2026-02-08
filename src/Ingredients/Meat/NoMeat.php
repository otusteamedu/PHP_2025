<?php

namespace Shop\Ingredients\Meat;

class NoMeat implements BaseMeat
{

    public function getNutritionalInfo(): array
    {
        return [];
    }

    public function getType(): string
    {
        return 'no_meat';
    }

    public function getName(): string
    {
        return 'Нет мяса';
    }

    public function getPrice(): float
    {
        return 0;
    }
}