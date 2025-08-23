<?php

declare(strict_types=1);

namespace App\Domain\Models;

final readonly class Book
{
    public function __construct(
        private string $id,
        private string $title,
        private string $category,
        private int $price,
        private array $stock
    ) {
    }

    /**
     * Возвращает id книги
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Возвращает название книги
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Возвращает категорию книги
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * Возвращает цену книги в рублях
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * Возвращает информацию о наличии книги в магазинах
     */
    public function getStock(): array
    {
        return $this->stock;
    }

    /**
     * Проверяет, есть ли книга в наличии хотя бы в одном магазине
     */
    public function isInStock(): bool
    {
        return $this->getTotalStock() > 0;
    }

    /**
     * Возвращает общее количество книг во всех магазинах
     */
    public function getTotalStock(): int
    {
        return array_sum(array_column($this->stock, 'stock'));
    }

    /**
     * Преобразует объект книги в массив
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'category' => $this->category,
            'price' => $this->price,
            'stock' => $this->stock,
            'total_stock' => $this->getTotalStock(),
        ];
    }

    /**
     * Создает объект книги из массива данных
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? $data['sku'] ?? '',
            title: $data['title'] ?? '',
            category: $data['category'] ?? '',
            price: (int) ($data['price'] ?? 0),
            stock: $data['stock'] ?? []
        );
    }
}