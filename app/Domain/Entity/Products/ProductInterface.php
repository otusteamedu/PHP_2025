<?php
declare(strict_types=1);

namespace App\Domain\Entity\Products;

interface ProductInterface
{
    public function getName(): string;

    public function getPrice(): float;

    public function setName(string $name): void;

    public function setPrice(float $price): void;
}
