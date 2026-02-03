<?php

declare(strict_types=1);

namespace App\Domain\Product;

interface ProductInterface
{
    public function getName(): string;
    public function getPrice(): float;

    public function getBaseProduct(): ProductInterface;
}
