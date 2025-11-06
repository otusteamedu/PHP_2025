<?php

declare(strict_types=1);

namespace Restaurant\Product;

class HotDog extends BaseProduct
{
    public function __construct()
    {
        $this->description = 'Хот-дог';
        $this->price = 100;
    }
}
