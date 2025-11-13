<?php

declare(strict_types=1);

namespace App\DTO;

/**
 * Модель книги
 */
readonly class Book
{
    public function __construct(
        public string $title,
        public string $category,
        public int $price,
        public int $totalStock
    ) {}

    /**
     * Создает объект Book из данных Elasticsearch
     *
     * @param  array<string,mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        // Суммируем остатки по всем магазинам
        $totalStock = 0;
        if (isset($data['stock']) && is_array($data['stock'])) {
            foreach ($data['stock'] as $stockItem) {
                if (isset($stockItem['stock'])) {
                    $totalStock += (int) $stockItem['stock'];
                }
            }
        }

        return new self(
            title: $data['title'],
            category: $data['category'],
            price: $data['price'],
            totalStock: $totalStock
        );
    }
}
