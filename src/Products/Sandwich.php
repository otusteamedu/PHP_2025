<?php

declare(strict_types=1);

namespace App\Products;

class Sandwich extends AbstractProduct
{
    public function __construct()
    {
        $this->name = 'Basic Sandwich';
        $this->price = 450;
        $this->description = 'Классический сэндвич с хлебом и начинкой';
    }
}
