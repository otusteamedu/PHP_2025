<?php

namespace App\Application\Proxy;

use App\Domain\Entity\Interface\ProductInterface;

interface CookingServiceInterface
{
    public function cook(ProductInterface $product): void;
}
