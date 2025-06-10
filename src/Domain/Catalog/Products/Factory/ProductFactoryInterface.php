<?php
declare(strict_types=1);

namespace Domain\Catalog\Products\Factory;

use Domain\Catalog\Products\Entity\Product;

interface ProductFactoryInterface
{
    public function create(string $name, string $description, float $version): Product;
}