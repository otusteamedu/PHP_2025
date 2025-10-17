<?php

declare(strict_types=1);

namespace App\Domain;

final readonly class StockItem
{
    public function __construct(
        private string $shop,
        private int    $stock
    ) {}

    public function getShop(): string
    {
        return $this->shop;
    }

    public function getStock(): int
    {
        return $this->stock;
    }
}
