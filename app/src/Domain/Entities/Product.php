<?php
declare(strict_types=1);

namespace Pryaniki\App\Domain\Entities;

class Product
{
    public Stock $stock;
    public function __construct(
        public string $title,
        public string $sku,
        public string $category,
        public int $price,
        public array $arStock
    ) {
        $this->stock = new Stock(
            $arStock['shop'],
            $arStock['stock'],
        );
    }

    public function getStock(): int
    {
        return $this->stock->stock;
    }

    public function getShop(): string
    {
        return $this->stock->shop;
    }
}