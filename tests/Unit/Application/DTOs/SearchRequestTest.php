<?php

declare(strict_types=1);

namespace Tests\Unit\Application\DTOs;

use App\Application\DTOs\SearchRequest;
use PHPUnit\Framework\TestCase;

final class SearchRequestTest extends TestCase
{
    /**
     * Тестирует создание запроса поиска со всеми параметрами
     */
    public function testSearchRequestCreationWithAllParameters(): void
    {
        $request = new SearchRequest(
            query: 'test query',
            category: 'Fiction',
            maxPrice: 1000,
            inStock: true
        );

        $this->assertEquals('test query', $request->getQuery());
        $this->assertEquals('Fiction', $request->getCategory());
        $this->assertEquals(1000, $request->getMaxPrice());
        $this->assertTrue($request->isInStock());
    }

    /**
     * Тестирует создание запроса поиска со значениями по умолчанию
     */
    public function testSearchRequestCreationWithDefaultValues(): void
    {
        $request = new SearchRequest();

        $this->assertNull($request->getQuery());
        $this->assertNull($request->getCategory());
        $this->assertNull($request->getMaxPrice());
        $this->assertFalse($request->isInStock());
    }

    /**
     * Тестирует правильную структуру массива при преобразовании в toArray
     */
    public function testToArrayReturnsCorrectStructure(): void
    {
        $request = new SearchRequest(
            query: 'test query',
            category: 'Fiction',
            maxPrice: 1000,
            inStock: true
        );

        $expected = [
            'query' => 'test query',
            'category' => 'Fiction',
            'max_price' => 1000,
            'in_stock' => true,
        ];

        $this->assertEquals($expected, $request->toArray());
    }

    /**
     * Тестирует обработку null значений в toArray
     */
    public function testToArrayHandlesNullValues(): void
    {
        $request = new SearchRequest();

        $expected = [
            'query' => null,
            'category' => null,
            'max_price' => null,
            'in_stock' => false,
        ];

        $this->assertEquals($expected, $request->toArray());
    }
}
