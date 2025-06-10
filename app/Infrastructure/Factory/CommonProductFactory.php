<?php
declare(strict_types=1);

namespace Infrastructure\Factory;

use Domain\Catalog\Products\Entity\Product;
use Domain\Catalog\Products\ValueObject\Name;
use Domain\Catalog\Products\ValueObject\Description;
use Domain\Catalog\Products\ValueObject\Version;

class CommonProductFactory
{
    public function create(string $name, string $description, float $version): Product
    {
        return new Product(
            new Name($name),
            new Description($description),
            new Version($version),
        );
    }
}
