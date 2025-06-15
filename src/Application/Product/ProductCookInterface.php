<?php

namespace App\Application\Product;

use App\Domain\Entity\Product;

interface ProductCookInterface
{
    public function cook(): Product;
}