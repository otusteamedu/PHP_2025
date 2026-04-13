<?php

namespace App\Domain\Product\Strategy;

interface ProductStrategyFactoryInterface
{
    public function create(string $productType): ProductStrategyInterface;
}