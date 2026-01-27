<?php

declare(strict_types=1);


namespace Dinargab\Homework14\Domain\Entity;

use Dinargab\Homework14\Domain\ValueObject\Sku;
use Dinargab\Homework14\Domain\ValueObject\StockCollection;

class Book
{
    private string $title;
    private Sku $sku;
    private string $category;
    private int $price;
    private StockCollection $stock;

    public function __construct(string $title, Sku $sku, string $category, int $price, StockCollection $stock)
    {
        $this->title    = $title;
        $this->sku      = $sku;
        $this->category = $category;
        $this->price    = $price;
        $this->stock    = $stock;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSku(): Sku
    {
        return $this->sku;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getStock(): StockCollection
    {
        return $this->stock;
    }
}