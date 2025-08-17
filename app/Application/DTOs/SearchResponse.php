<?php

declare(strict_types=1);

namespace App\Application\DTOs;

use App\Domain\Models\Book;

final readonly class SearchResponse
{
    public function __construct(
        private array $books,
        private int $total,
        private float $took
    ) {
    }

    /**
     * Возвращает найденные книги
     */
    public function getBooks(): array
    {
        return $this->books;
    }

    /**
     * Возвращает общее количество найденных книг
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * Возвращает время выполнения поиска в миллисекундах
     */
    public function getTook(): float
    {
        return $this->took;
    }

    /**
     * Проверяет, пустой ли результат поиска
     */
    public function isEmpty(): bool
    {
        return empty($this->books);
    }

    /**
     * Преобразует ответ в массив для вывода
     */
    public function toArray(): array
    {
        return [
            'books' => array_map(static fn(Book $book) => $book->toArray(), $this->books),
            'total' => $this->total,
            'took' => $this->took,
        ];
    }
}