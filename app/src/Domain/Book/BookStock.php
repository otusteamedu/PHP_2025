<?php
declare(strict_types=1);


namespace App\Domain\Book;

class BookStock
{
    public function __construct(
        private readonly string $shop,
        private readonly int    $stock,
    )
    {
    }

    public function getShop(): string
    {
        return $this->shop;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

}