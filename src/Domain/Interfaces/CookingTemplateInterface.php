<?php

namespace App\Domain\Interfaces;

use App\Domain\Entities\Product;

interface CookingTemplateInterface
{
    public function cook(Product $product): Product;
}