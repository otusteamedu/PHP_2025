<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Validators;

use App\Application\Validators\SearchCriteriaValidator;
use App\Domain\Models\SearchCriteria;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class SearchCriteriaValidatorTest extends TestCase
{
    private SearchCriteriaValidator $validator;

    /**
     * Настройка тестового окружения
     */
    protected function setUp(): void
    {
        $this->validator = new SearchCriteriaValidator();
    }

    /**
     * Тестирует, что валидация проходит с корректными критериями
     */
    public function testValidatePassesWithValidCriteria(): void
    {
        $criteria = new SearchCriteria(
            query: 'test query',
            category: 'Fiction',
            maxPrice: 1000,
            inStock: true
        );

        $this->validator->validate($criteria);
        $this->assertTrue(true);
    }

    /**
     * Тестирует, что валидация проходит с null максимальной ценой
     */
    public function testValidatePassesWithNullMaxPrice(): void
    {
        $criteria = new SearchCriteria(
            query: 'test query',
            maxPrice: null
        );

        $this->validator->validate($criteria);
        $this->assertTrue(true);
    }

    /**
     * Тестирует, что валидация проходит с нулевой максимальной ценой
     */
    public function testValidatePassesWithZeroMaxPrice(): void
    {
        $criteria = new SearchCriteria(
            query: 'test query',
            maxPrice: 0
        );

        $this->validator->validate($criteria);
        $this->assertTrue(true);
    }

    /**
     * Тестирует, что валидация выбрасывает исключение с отрицательной ценой
     */
    public function testValidateThrowsExceptionWithNegativeMaxPrice(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Максимальная цена не может быть отрицательной');

        $criteria = new SearchCriteria(
            query: 'test query',
            maxPrice: -100
        );

        $this->validator->validate($criteria);
    }

    /**
     * Тестирует, что валидация выбрасывает исключение с большой отрицательной ценой
     */
    public function testValidateThrowsExceptionWithLargeNegativeMaxPrice(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Максимальная цена не может быть отрицательной');

        $criteria = new SearchCriteria(
            query: 'test query',
            maxPrice: -1000000
        );

        $this->validator->validate($criteria);
    }
}
