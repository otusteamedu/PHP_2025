<?php
declare(strict_types=1);

namespace App\Domain\Patterns\AbstractFactory;

use App\Domain\Entity\Products\ProductInterface;

interface ProductFactoryInterface
{
    public function createOriginal(): ProductInterface;

    public function createSpicy(): ProductInterface;
}