<?php

declare(strict_types=1);

namespace Tests\Unit\Application\DTOs;

use App\Application\DTOs\SearchResponse;
use App\Domain\Models\Book;
use PHPUnit\Framework\TestCase;

final class SearchResponseTest extends TestCase
{
    /**
     * Тестирует создание ответа поиска
     */
    public function testSearchResponseCreation(): void
    {
        $books = [
            new Book('1', 'Book 1', 'Fiction', 1000, []),
            new Book('2', 'Book 2', 'Non-Fiction', 1500, [])
        ];

        $response = new SearchResponse(
            books: $books,
            total: 2,
            took: 150.5
        );

        $this->assertEquals($books, $response->getBooks());
        $this->assertEquals(2, $response->getTotal());
        $this->assertEquals(150.5, $response->getTook());
    }

    /**
     * Тестирует, что isEmpty возвращает true когда нет книг
     */
    public function testIsEmptyReturnsTrueWhenNoBooks(): void
    {
        $response = new SearchResponse(
            books: [],
            total: 0,
            took: 0.0
        );

        $this->assertTrue($response->isEmpty());
    }

    /**
     * Тестирует, что isEmpty возвращает false когда есть книги
     */
    public function testIsEmptyReturnsFalseWhenBooksExist(): void
    {
        $books = [
            new Book('1', 'Book 1', 'Fiction', 1000, [])
        ];

        $response = new SearchResponse(
            books: $books,
            total: 1,
            took: 50.0
        );

        $this->assertFalse($response->isEmpty());
    }

    /**
     * Тестирует правильную структуру массива при преобразовании в toArray
     */
    public function testToArrayReturnsCorrectStructure(): void
    {
        $books = [
            new Book('1', 'Book 1', 'Fiction', 1000, [
                ['store' => 'Store 1', 'stock' => 5]
            ]),
            new Book('2', 'Book 2', 'Non-Fiction', 1500, [
                ['store' => 'Store 2', 'stock' => 3]
            ])
        ];

        $response = new SearchResponse(
            books: $books,
            total: 2,
            took: 150.5
        );

        $result = $response->toArray();

        $this->assertArrayHasKey('books', $result);
        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('took', $result);

        $this->assertEquals(2, $result['total']);
        $this->assertEquals(150.5, $result['took']);
        $this->assertCount(2, $result['books']);

        // Проверяем структуру первой книги
        $firstBook = $result['books'][0];
        $this->assertEquals('1', $firstBook['id']);
        $this->assertEquals('Book 1', $firstBook['title']);
        $this->assertEquals('Fiction', $firstBook['category']);
        $this->assertEquals(1000, $firstBook['price']);
        $this->assertEquals(5, $firstBook['total_stock']);
    }

    /**
     * Тестирует обработку пустого массива книг в toArray
     */
    public function testToArrayHandlesEmptyBooksArray(): void
    {
        $response = new SearchResponse(
            books: [],
            total: 0,
            took: 0.0
        );

        $result = $response->toArray();

        $this->assertEquals([], $result['books']);
        $this->assertEquals(0, $result['total']);
        $this->assertEquals(0.0, $result['took']);
    }
}
