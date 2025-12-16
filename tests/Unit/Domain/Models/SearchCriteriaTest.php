<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Models;

use App\Domain\Models\SearchCriteria;
use PHPUnit\Framework\TestCase;

final class SearchCriteriaTest extends TestCase
{
    /**
     * Тестирует создание критериев поиска со всеми параметрами
     */
    public function testSearchCriteriaCreationWithAllParameters(): void
    {
        $criteria = new SearchCriteria(
            query: 'test query',
            category: 'Fiction',
            maxPrice: 1000,
            inStock: true
        );

        $this->assertEquals('test query', $criteria->getQuery());
        $this->assertEquals('Fiction', $criteria->getCategory());
        $this->assertEquals(1000, $criteria->getMaxPrice());
        $this->assertTrue($criteria->isInStock());
    }

    /**
     * Тестирует создание критериев поиска с значениями по умолчанию
     */
    public function testSearchCriteriaCreationWithDefaultValues(): void
    {
        $criteria = new SearchCriteria();

        $this->assertNull($criteria->getQuery());
        $this->assertNull($criteria->getCategory());
        $this->assertNull($criteria->getMaxPrice());
        $this->assertFalse($criteria->isInStock());
    }

    /**
     * Тестирует, что hasQuery возвращает true когда запрос не пустой
     */
    public function testHasQueryReturnsTrueWhenQueryIsNotEmpty(): void
    {
        $criteria = new SearchCriteria(query: 'test query');
        $this->assertTrue($criteria->hasQuery());
    }

    /**
     * Тестирует, что hasQuery возвращает false когда запрос null
     */
    public function testHasQueryReturnsFalseWhenQueryIsNull(): void
    {
        $criteria = new SearchCriteria(query: null);
        $this->assertFalse($criteria->hasQuery());
    }

    /**
     * Тестирует, что hasQuery возвращает false когда запрос пустой
     */
    public function testHasQueryReturnsFalseWhenQueryIsEmpty(): void
    {
        $criteria = new SearchCriteria(query: '');
        $this->assertFalse($criteria->hasQuery());
    }

    /**
     * Тестирует, что hasQuery возвращает false когда запрос только пробелы
     */
    public function testHasQueryReturnsFalseWhenQueryIsOnlyWhitespace(): void
    {
        $criteria = new SearchCriteria(query: '   ');
        $this->assertFalse($criteria->hasQuery());
    }

    /**
     * Тестирует, что hasCategory возвращает true когда категория не пустая
     */
    public function testHasCategoryReturnsTrueWhenCategoryIsNotEmpty(): void
    {
        $criteria = new SearchCriteria(category: 'Fiction');
        $this->assertTrue($criteria->hasCategory());
    }

    /**
     * Тестирует, что hasCategory возвращает false когда категория null
     */
    public function testHasCategoryReturnsFalseWhenCategoryIsNull(): void
    {
        $criteria = new SearchCriteria(category: null);
        $this->assertFalse($criteria->hasCategory());
    }

    /**
     * Тестирует, что hasCategory возвращает false когда категория пустая
     */
    public function testHasCategoryReturnsFalseWhenCategoryIsEmpty(): void
    {
        $criteria = new SearchCriteria(category: '');
        $this->assertFalse($criteria->hasCategory());
    }

    /**
     * Тестирует, что hasCategory возвращает false когда категория только пробелы
     */
    public function testHasCategoryReturnsFalseWhenCategoryIsOnlyWhitespace(): void
    {
        $criteria = new SearchCriteria(category: '   ');
        $this->assertFalse($criteria->hasCategory());
    }

    /**
     * Тестирует, что hasMaxPrice возвращает true когда максимальная цена положительная
     */
    public function testHasMaxPriceReturnsTrueWhenMaxPriceIsPositive(): void
    {
        $criteria = new SearchCriteria(maxPrice: 1000);
        $this->assertTrue($criteria->hasMaxPrice());
    }

    /**
     * Тестирует, что hasMaxPrice возвращает false когда максимальная цена null
     */
    public function testHasMaxPriceReturnsFalseWhenMaxPriceIsNull(): void
    {
        $criteria = new SearchCriteria(maxPrice: null);
        $this->assertFalse($criteria->hasMaxPrice());
    }

    /**
     * Тестирует, что hasMaxPrice возвращает false когда максимальная цена равна нулю
     */
    public function testHasMaxPriceReturnsFalseWhenMaxPriceIsZero(): void
    {
        $criteria = new SearchCriteria(maxPrice: 0);
        $this->assertFalse($criteria->hasMaxPrice());
    }

    /**
     * Тестирует, что hasMaxPrice возвращает false когда максимальная цена отрицательная
     */
    public function testHasMaxPriceReturnsFalseWhenMaxPriceIsNegative(): void
    {
        $criteria = new SearchCriteria(maxPrice: -100);
        $this->assertFalse($criteria->hasMaxPrice());
    }

    /**
     * Тестирует правильную структуру массива при преобразовании в toArray
     */
    public function testToArrayReturnsCorrectStructure(): void
    {
        $criteria = new SearchCriteria(
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

        $this->assertEquals($expected, $criteria->toArray());
    }

    /**
     * Тестирует обработку null значений в toArray
     */
    public function testToArrayHandlesNullValues(): void
    {
        $criteria = new SearchCriteria();

        $expected = [
            'query' => null,
            'category' => null,
            'max_price' => null,
            'in_stock' => false,
        ];

        $this->assertEquals($expected, $criteria->toArray());
    }
}
