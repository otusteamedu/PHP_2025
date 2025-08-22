<?php

declare(strict_types=1);

namespace App\Products;

class Hotdog extends AbstractProduct
{
    public function __construct()
    {
        $this->name = 'Basic Hotdog';
        $this->price = 350;
        $this->description = 'Классический хот-дог с сосиской и булочкой';
    }
}
