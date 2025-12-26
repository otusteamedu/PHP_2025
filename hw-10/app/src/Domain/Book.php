<?php

declare(strict_types=1);

namespace App\Domain;

final class Book
{
    /**
     * @param list<Shop> $stock
     */
    public function __construct(
        public string $title,
        public string $sku,
        public string $category,
        public int $price,
        public array $stock,
    ) {
    }
}
