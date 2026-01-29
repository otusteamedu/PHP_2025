<?php

declare(strict_types=1);

namespace Dinargab\Homework14\Application\DTO;

class SearchBookRequestDTO
{
    public function __construct(
        public readonly string $searchQuery,
        public readonly ?int $minPrice,
        public readonly ?int $maxPrice,
        public readonly ?string $category,
        public readonly bool $inStock,
    ) {
    }
}