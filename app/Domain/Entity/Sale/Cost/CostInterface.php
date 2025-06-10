<?php
declare(strict_types=1);

namespace App\Domain\Entity\Sale\Cost;

use App\Domain\Entity\Products\ProductCollection;

interface CostInterface
{

    public function getOrderAmount(ProductCollection $products): float;
    public function getMessagePrices(): string;
}