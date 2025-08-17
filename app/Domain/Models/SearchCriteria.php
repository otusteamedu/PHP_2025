<?php

declare(strict_types=1);

namespace App\Domain\Models;

final readonly class SearchCriteria
{
    public function __construct(
        private ?string $query = null,
        private ?string $category = null,
        private ?int $maxPrice = null,
        private bool $inStock = false
    ) {
    }

    /**
     * Возвращает поисковый запрос
     */
    public function getQuery(): ?string
    {
        return $this->query;
    }

    /**
     * Возвращает категорию для фильтрации
     */
    public function getCategory(): ?string
    {
        return $this->category;
    }

    /**
     * Возвращает максимальную цену для фильтрации
     */
    public function getMaxPrice(): ?int
    {
        return $this->maxPrice;
    }

    /**
     * Проверяет, нужно ли искать только книги в наличии
     */
    public function isInStock(): bool
    {
        return $this->inStock;
    }

    /**
     * Проверяет, задан ли поисковый запрос
     */
    public function hasQuery(): bool
    {
        return $this->query !== null && trim($this->query) !== '';
    }

    /**
     * Проверяет, задана ли категория для фильтрации
     */
    public function hasCategory(): bool
    {
        return $this->category !== null && trim($this->category) !== '';
    }

    /**
     * Проверяет, задана ли максимальная цена
     */
    public function hasMaxPrice(): bool
    {
        return $this->maxPrice !== null && $this->maxPrice > 0;
    }

    /**
     * Преобразует критерии поиска в массив
     */
    public function toArray(): array
    {
        return [
            'query' => $this->query,
            'category' => $this->category,
            'max_price' => $this->maxPrice,
            'in_stock' => $this->inStock,
        ];
    }
}
