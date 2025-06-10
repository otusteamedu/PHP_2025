<?php
declare(strict_types=1);

namespace App\Domain\Entity\Sale\Cost;

use App\Domain\Entity\Products\ProductCollection;
use App\Domain\Entity\Products\ProductInterface;

class DiscountedPrice implements CostInterface
{

    public function __construct(private readonly int $discount = 0)
    {
    }

    public function getOrderAmount(ProductCollection $products): float
    {
        $price = 0;
        foreach ($products->getProductCollection() as $product) {
            /** @var ProductInterface $product */
            $price += $product->getPrice();
        }

        return (float)($this->discount ? $price - $price * ($this->discount / 100) : $price);
    }

    public function getMessagePrices(): string
    {
        return ' со скидкой ' . ($this->discount ? '[' . $this->discount . '%]' : '') . ' ';
    }
}