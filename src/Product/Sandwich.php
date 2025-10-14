<?php

declare(strict_types=1);

namespace Restaurant\Product;

class Sandwich extends BaseProduct
{
    public function __construct()
    {
        $this->description = 'Сэндвич';
        $this->price = 120;
    }
}
