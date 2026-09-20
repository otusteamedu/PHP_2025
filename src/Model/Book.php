<?php

declare(strict_types=1);

namespace App\Model;

/**
 * Модель книги.
 * Представляет товар книжного магазина.
 */
final readonly class Book
{
    public function __construct(
        /** Артикул книги */
        public string $sku,
        /** Название книги */
        public string $title,
        /** Категория (жанр) */
        public string $category,
        /** Цена в рублях */
        public int $price,
        /** Количество на складе */
        public int $stock,
    ) {
    }

    /**
     * Создать объект Book из массива данных.
     *
     * @param array<string, mixed> $data Массив с данными книги
     * @return self
     */
    public static function fromArray(array $data): self
    {
        // Вычисляем общее количество на складе из всех магазинов
        $stock = 0;

        if (isset($data['stock']) && is_array($data['stock'])) {
            foreach ($data['stock'] as $stockRow) {
                if (is_array($stockRow) && isset($stockRow['stock'])) {
                    $stock += (int) $stockRow['stock'];
                }
            }
        }

        return new self(
            sku: (string) ($data['sku'] ?? ''),
            title: trim((string) ($data['title'] ?? '')),
            category: trim((string) ($data['category'] ?? '')),
            price: (int) ($data['price'] ?? 0),
            stock: $stock,
        );
    }

    /**
     * Преобразовать в массив для индексации в Elasticsearch.
     *
     * @return array<string, string|int>
     */
    public function toDocument(): array
    {
        return [
            'sku' => $this->sku,
            'title' => $this->title,
            'category' => $this->category,
            'price' => $this->price,
            'stock' => $this->stock,
        ];
    }
}
