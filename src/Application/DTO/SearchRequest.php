<?php

declare(strict_types=1);

namespace App\Application\DTO;

final readonly class SearchRequest
{
    public function __construct(
        public ?string $query = null,
        public ?string $category = null,
        public ?int    $minPrice = null,
        public ?int    $maxPrice = null,
        public ?int    $exactPrice = null,
        public bool    $inStockOnly = true,
        public int     $size = 20,
        public int     $from = 0,
    ) {}
}
