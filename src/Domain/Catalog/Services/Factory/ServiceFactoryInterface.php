<?php
declare(strict_types=1);

namespace Domain\Catalog\Services\Factory;

use Domain\Catalog\Products\Entity\Product;
use Domain\Catalog\Services\Entity\Service;

interface ServiceFactoryInterface
{
    public function create(Product $product, float $price, string $typeOfInstallation): Service;
}