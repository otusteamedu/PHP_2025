<?php
declare(strict_types=1);

namespace Domain\Catalog\Services\ValueObject;

use Domain\Catalog\Products\Entity\Product;

class ConnectionProduct
{
    private Product $value;

    public function __construct(Product $value)
    {
        $this->assertValidName($value);
        $this->value = $value;
    }

    public function getValue(): Product
    {
        return $this->value;
    }

    private function assertValidName(Product $value): void
    {
        if ($value->getId() === null) {
            throw new \InvalidArgumentException('Error Services ConnectionProduct');
        }
    }
}
