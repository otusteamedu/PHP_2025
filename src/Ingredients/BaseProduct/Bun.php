<?php

namespace Shop\Ingredients\BaseProduct;

class Bun implements Base
{
    public function getName(): string
    {
        return "Булка";
    }

    public function hasSesame(): bool
    {
        return true;
    }

    public function getPrice(): float
    {
        return 100;
    }
}