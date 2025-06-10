<?php
declare(strict_types=1);

namespace Infrastructure\Factory;

use Domain\Catalog\Products\Entity\Product;
use Domain\Catalog\Services\Entity\Service;
use Domain\Catalog\Services\ValueObject\ConnectionProduct;
use Domain\Catalog\Services\ValueObject\Price;
use Domain\Catalog\Services\ValueObject\TypeOfInstallation;

class CommonServiceFactory
{
    public function create(Product $product, float $price, string $typeOfInstallation): Service
    {
        return new Service(
            new ConnectionProduct($product),
            new Price($price),
            new TypeOfInstallation($typeOfInstallation),
        );
    }
}
