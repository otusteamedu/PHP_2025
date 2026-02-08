<?php

namespace Shop\Ingredients\BaseProduct;

class HotDogBun implements Base
{
    public function getName(): string
    {
        return "Булочка для хот-дога";
    }

    public function hasSesame(): bool
    {
        return false;
    }

    public function getPrice(): float
    {
        return 50;
    }
}