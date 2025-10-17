<?php

declare(strict_types=1);

namespace App\Domain;

final class Book
{
    /** @var StockItem[] */
    private array $stock;

    public function __construct(
        private readonly string $title,
        private readonly string $sku,
        private readonly string $category,
        private readonly int    $price,
        array                   $stock
    ) {
        $this->stock = $stock;
    }

    public function getTitle(): string { return $this->title; }
    public function getSku(): string { return $this->sku; }
    public function getCategory(): string { return $this->category; }
    public function getPrice(): int { return $this->price; }
    /**
     * @return StockItem[]
     */
    public function getStock(): array { return $this->stock; }

    public function getTotalStock(): int
    {
        $total = 0;
        foreach ($this->stock as $item) {
            $total += $item->getStock();
        }
        return $total;
    }
}
