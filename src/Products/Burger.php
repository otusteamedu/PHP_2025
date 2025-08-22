<?php

declare(strict_types=1);

namespace App\Products;

class Burger extends AbstractProduct
{
    public function __construct()
    {
        $this->name = 'Basic Burger';
        $this->price = 550;
        $this->description = 'Классический бургер с булочкой и котлетой';
    }
}
