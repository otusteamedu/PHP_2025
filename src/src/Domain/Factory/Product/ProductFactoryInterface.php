<?php

namespace App\Domain\Factory\Product;

interface ProductFactoryInterface  
{  
    public function makeProduct(string $title); 
}