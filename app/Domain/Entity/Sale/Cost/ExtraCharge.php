<?php
declare(strict_types=1);

namespace App\Domain\Entity\Sale\Cost;

use App\Domain\Entity\Products\ProductCollection;
use App\Domain\Entity\Products\ProductInterface;

class ExtraCharge implements CostInterface
{

    public function __construct(private readonly int $additionalCharge = 0)
    {
    }

    public function getOrderAmount(ProductCollection $products): float
    {
        $price = 0;
        foreach ($products->getProductCollection() as $product) {
            /** @var ProductInterface $product */
            $price += $product->getPrice();
        }

        return (float)($price + $this->additionalCharge);
    }


    public function getMessagePrices(): string
    {
        return ' с дополнительной платой ' . ($this->additionalCharge ? '[' . $this->additionalCharge . ']' : '') . ' ';
    }
}