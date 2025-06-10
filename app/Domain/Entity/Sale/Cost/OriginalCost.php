<?php
declare(strict_types=1);

namespace App\Domain\Entity\Sale\Cost;

use App\Domain\Entity\Products\ProductCollection;
use App\Domain\Entity\Products\ProductInterface;

class OriginalCost implements CostInterface
{
    public function __construct()
    {
    }

    public function getOrderAmount(ProductCollection $products): float
    {
        $price = 0;
        foreach ($products->getProductCollection() as $product) {
            /** @var ProductInterface $product */
            $price += $product->getPrice();
        }

        return (float)$price;
    }

    public function getMessagePrices(): string
    {
        return '';
    }
}