<?php

namespace Shop\Ingredients\BaseProduct;

class Dough implements Base
{
    public function getName(): string
    {
        return "Тесто";
    }

    public function hasSesame(): bool
    {
        return false;
    }

    public function getPrice(): float
    {
        return 101;
    }
}