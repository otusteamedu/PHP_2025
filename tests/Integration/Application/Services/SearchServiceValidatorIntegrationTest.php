<?php

declare(strict_types=1);

namespace Tests\Integration\Application\Services;

use App\Application\DTOs\SearchRequest;
use App\Application\Services\SearchService;
use App\Application\Validators\SearchCriteriaValidator;
use App\Domain\Models\Book;
use App\Domain\Repositories\BookRepositoryInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class SearchServiceValidatorIntegrationTest extends TestCase
{
    private SearchService $searchService;
    private BookRepositoryInterface $mockRepository;
    private SearchCriteriaValidator $validator;

    /**
     * Настройка тестового окружения
     */
    protected function setUp(): void
    {
        $this->mockRepository = $this->createMock(BookRepositoryInterface::class);
        $this->validator = new SearchCriteriaValidator();
        $this->searchService = new SearchService($this->mockRepository, $this->validator);
    }

    /**
     * Тестирует интеграцию поиска с валидацией корректных данных
     */
    public function testSearchWithValidCriteriaIntegration(): void
    {
        // Arrange
        $request = new SearchRequest(
            query: 'PHP Programming',
            category: 'Programming',
            maxPrice: 2000,
            inStock: true
        );

        $expectedBooks = [
            new Book('1', 'PHP Programming', 'Programming', 1500, [['store' => 'Store 1', 'stock' => 5]]),
            new Book('2', 'JavaScript Basics', 'Programming', 1200, [['store' => 'Store 2', 'stock' => 3]])
        ];

        $this->mockRepository
            ->expects($this->once())
            ->method('search')
            ->willReturn($expectedBooks);

        // Act
        $response = $this->searchService->search($request);

        // Assert
        $this->assertEquals(2, $response->getTotal());
        $this->assertEquals($expectedBooks, $response->getBooks());
        $this->assertGreaterThan(0, $response->getTook());
    }

    /**
     * Тестирует интеграцию поиска с валидацией некорректной цены
     */
    public function testSearchWithInvalidPriceIntegration(): void
    {
        // Arrange
        $request = new SearchRequest(
            query: 'PHP Programming',
            maxPrice: -100
        );

        // Act & Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Максимальная цена не может быть отрицательной');

        $this->searchService->search($request);
    }

    /**
     * Тестирует интеграцию поиска с валидацией нулевой цены
     */
    public function testSearchWithZeroPriceIntegration(): void
    {
        // Arrange
        $request = new SearchRequest(
            query: 'PHP Programming',
            maxPrice: 0
        );

        $expectedBooks = [
            new Book('1', 'PHP Programming', 'Programming', 1500, [['store' => 'Store 1', 'stock' => 5]])
        ];

        $this->mockRepository
            ->expects($this->once())
            ->method('search')
            ->willReturn($expectedBooks);

        // Act
        $response = $this->searchService->search($request);

        // Assert
        $this->assertEquals(1, $response->getTotal());
        $this->assertGreaterThan(0, $response->getTook());
    }

    /**
     * Тестирует интеграцию поиска с валидацией null цены
     */
    public function testSearchWithNullPriceIntegration(): void
    {
        // Arrange
        $request = new SearchRequest(
            query: 'PHP Programming',
            maxPrice: null
        );

        $expectedBooks = [
            new Book('1', 'PHP Programming', 'Programming', 1500, [['store' => 'Store 1', 'stock' => 5]])
        ];

        $this->mockRepository
            ->expects($this->once())
            ->method('search')
            ->willReturn($expectedBooks);

        // Act
        $response = $this->searchService->search($request);

        // Assert
        $this->assertEquals(1, $response->getTotal());
        $this->assertGreaterThan(0, $response->getTook());
    }

    /**
     * Тестирует интеграцию поиска с пустыми критериями
     */
    public function testSearchWithEmptyCriteriaIntegration(): void
    {
        // Arrange
        $request = new SearchRequest();

        $expectedBooks = [
            new Book('1', 'Test Book', 'Fiction', 1000, [['store' => 'Store 1', 'stock' => 1]])
        ];

        $this->mockRepository
            ->expects($this->once())
            ->method('search')
            ->willReturn($expectedBooks);

        // Act
        $response = $this->searchService->search($request);

        // Assert
        $this->assertEquals(1, $response->getTotal());
        $this->assertGreaterThan(0, $response->getTook());
    }

    /**
     * Тестирует интеграцию поиска с большим количеством результатов
     */
    public function testSearchWithLargeResultSetIntegration(): void
    {
        // Arrange
        $request = new SearchRequest(query: 'Programming');

        $expectedBooks = [];
        for ($i = 1; $i <= 50; $i++) {
            $expectedBooks[] = new Book(
                (string) $i,
                "Programming Book {$i}",
                'Programming',
                1000 + $i,
                [['store' => 'Store 1', 'stock' => 1]]
            );
        }

        $this->mockRepository
            ->expects($this->once())
            ->method('search')
            ->willReturn($expectedBooks);

        // Act
        $response = $this->searchService->search($request);

        // Assert
        $this->assertEquals(50, $response->getTotal());
        $this->assertCount(50, $response->getBooks());
        $this->assertGreaterThan(0, $response->getTook());
    }
}
