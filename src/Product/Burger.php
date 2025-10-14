<?php

declare(strict_types=1);

namespace Restaurant\Product;

class Burger extends BaseProduct
{
    public function __construct()
    {
        $this->description = 'Бургер';
        $this->price = 150;
    }
}
