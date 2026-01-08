<?php

namespace Arlex2305k\BooksShop\Entity;

class Book
{
    private string $sku;
    private string $title;
    private string $category;
    private float $price;
    private array $stock;

    public function __construct(
        string $sku,
        string $title,
        string $category,
        float $price,
        array $stock
    ) {
        $this->sku = $sku;
        $this->title = $title;
        $this->category = $category;
        $this->price = $price;
        $this->stock = $stock;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getStock(): array
    {
        return $this->stock;
    }

    public function getAvailableStock(): int
    {
        $total = 0;
        foreach ($this->stock as $shopStock) {
            if (isset($shopStock['stock'])) {
                $total += (int)$shopStock['stock'];
            }
        }
        return $total;
    }
}