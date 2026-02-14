<?php

declare(strict_types=1);

namespace App\Domain\Interfaces;

use App\Domain\Interfaces\ProductInterface;

interface ProductStrategyInterface
{
    public function createProduct(): ProductInterface;

    public function getType(): string;
}
