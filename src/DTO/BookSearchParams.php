<?php

declare(strict_types=1);

namespace App\DTO;

/**
 * Параметры поиска книг
 */
readonly class BookSearchParams implements SearchParamsInterface
{
    public function __construct(
        public ?string $query = null,
        public ?string $category = null,
        public ?int $maxPrice = null,
        public bool $inStock = false
    ) {}

    /**
     * Проверяет, что хотя бы один параметр поиска задан
     */
    public function hasSearchCriteria(): bool
    {
        return $this->query !== null
            || $this->category !== null
            || $this->maxPrice !== null
            || $this->inStock;
    }
}
