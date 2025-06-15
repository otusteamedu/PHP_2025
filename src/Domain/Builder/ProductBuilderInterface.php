<?php

namespace App\Domain\Builder;

use App\Domain\Entity\Product;

interface ProductBuilderInterface
{
    public function build(): Product;
}