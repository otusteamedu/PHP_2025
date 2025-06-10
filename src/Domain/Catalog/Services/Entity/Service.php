<?php
declare(strict_types=1);

namespace Domain\Catalog\Services\Entity;

use Domain\Catalog\Services\ValueObject\ConnectionProduct;
use Domain\Catalog\Services\ValueObject\Price;
use Domain\Catalog\Services\ValueObject\TypeOfInstallation;

class Service
{

    private ?int $id = null;

    public function __construct(
        private readonly ConnectionProduct $product,
        private readonly Price $price,
        private readonly TypeOfInstallation $typeOfInstallation)
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ConnectionProduct
    {
        return $this->product;
    }

    public function getPrice(): Price
    {
        return $this->price;
    }

    public function getTypeOfInstallation(): TypeOfInstallation
    {
        return $this->typeOfInstallation;
    }
}