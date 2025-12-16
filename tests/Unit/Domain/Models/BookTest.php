<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Models;

use App\Domain\Models\Book;
use PHPUnit\Framework\TestCase;

final class BookTest extends TestCase
{
    /**
     * Тестирует создание объекта книги
     */
    public function testBookCreation(): void
    {
        $book = new Book(
            id: 'test-123',
            title: 'Test Book',
            category: 'Fiction',
            price: 1000,
            stock: [
                ['store' => 'Store 1', 'stock' => 5],
                ['store' => 'Store 2', 'stock' => 3]
            ]
        );

        $this->assertEquals('test-123', $book->getId());
        $this->assertEquals('Test Book', $book->getTitle());
        $this->assertEquals('Fiction', $book->getCategory());
        $this->assertEquals(1000, $book->getPrice());
        $this->assertEquals([
            ['store' => 'Store 1', 'stock' => 5],
            ['store' => 'Store 2', 'stock' => 3]
        ], $book->getStock());
    }

    /**
     * Тестирует, что isInStock возвращает true когда есть товар в наличии
     */
    public function testIsInStockReturnsTrueWhenStockAvailable(): void
    {
        $book = new Book(
            id: 'test-123',
            title: 'Test Book',
            category: 'Fiction',
            price: 1000,
            stock: [
                ['store' => 'Store 1', 'stock' => 5],
                ['store' => 'Store 2', 'stock' => 3]
            ]
        );

        $this->assertTrue($book->isInStock());
    }

    /**
     * Тестирует, что isInStock возвращает false когда нет товара в наличии
     */
    public function testIsInStockReturnsFalseWhenNoStock(): void
    {
        $book = new Book(
            id: 'test-123',
            title: 'Test Book',
            category: 'Fiction',
            price: 1000,
            stock: [
                ['store' => 'Store 1', 'stock' => 0],
                ['store' => 'Store 2', 'stock' => 0]
            ]
        );

        $this->assertFalse($book->isInStock());
    }

    /**
     * Тестирует правильный расчет общего количества товара
     */
    public function testGetTotalStockCalculatesCorrectly(): void
    {
        $book = new Book(
            id: 'test-123',
            title: 'Test Book',
            category: 'Fiction',
            price: 1000,
            stock: [
                ['store' => 'Store 1', 'stock' => 5],
                ['store' => 'Store 2', 'stock' => 3],
                ['store' => 'Store 3', 'stock' => 2]
            ]
        );

        $this->assertEquals(10, $book->getTotalStock());
    }

    /**
     * Тестирует правильную структуру массива при преобразовании в toArray
     */
    public function testToArrayReturnsCorrectStructure(): void
    {
        $book = new Book(
            id: 'test-123',
            title: 'Test Book',
            category: 'Fiction',
            price: 1000,
            stock: [
                ['store' => 'Store 1', 'stock' => 5],
                ['store' => 'Store 2', 'stock' => 3]
            ]
        );

        $expected = [
            'id' => 'test-123',
            'title' => 'Test Book',
            'category' => 'Fiction',
            'price' => 1000,
            'stock' => [
                ['store' => 'Store 1', 'stock' => 5],
                ['store' => 'Store 2', 'stock' => 3]
            ],
            'total_stock' => 8,
        ];

        $this->assertEquals($expected, $book->toArray());
    }

    /**
     * Тестирует создание книги из массива данных
     */
    public function testFromArrayCreatesBookCorrectly(): void
    {
        $data = [
            'id' => 'test-123',
            'title' => 'Test Book',
            'category' => 'Fiction',
            'price' => 1000,
            'stock' => [
                ['store' => 'Store 1', 'stock' => 5],
                ['store' => 'Store 2', 'stock' => 3]
            ]
        ];

        $book = Book::fromArray($data);

        $this->assertEquals('test-123', $book->getId());
        $this->assertEquals('Test Book', $book->getTitle());
        $this->assertEquals('Fiction', $book->getCategory());
        $this->assertEquals(1000, $book->getPrice());
        $this->assertEquals([
            ['store' => 'Store 1', 'stock' => 5],
            ['store' => 'Store 2', 'stock' => 3]
        ], $book->getStock());
    }

    /**
     * Тестирует использование SKU как ID когда ID отсутствует
     */
    public function testFromArrayUsesSkuAsIdWhenIdNotPresent(): void
    {
        $data = [
            'sku' => 'test-sku-123',
            'title' => 'Test Book',
            'category' => 'Fiction',
            'price' => 1000,
            'stock' => []
        ];

        $book = Book::fromArray($data);

        $this->assertEquals('test-sku-123', $book->getId());
    }

    /**
     * Тестирует обработку отсутствующих полей при создании из массива
     */
    public function testFromArrayHandlesMissingFields(): void
    {
        $data = [
            'title' => 'Test Book'
        ];

        $book = Book::fromArray($data);

        $this->assertEquals('', $book->getId());
        $this->assertEquals('Test Book', $book->getTitle());
        $this->assertEquals('', $book->getCategory());
        $this->assertEquals(0, $book->getPrice());
        $this->assertEquals([], $book->getStock());
    }

    /**
     * Тестирует преобразование цены в целое число
     */
    public function testFromArrayConvertsPriceToInteger(): void
    {
        $data = [
            'title' => 'Test Book',
            'price' => '1500.50'
        ];

        $book = Book::fromArray($data);

        $this->assertEquals(1500, $book->getPrice());
    }
}
