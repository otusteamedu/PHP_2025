<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Services;

use App\Application\DTOs\SearchRequest;
use App\Application\DTOs\SearchResponse;
use App\Application\Services\SearchService;
use App\Application\Validators\SearchCriteriaValidator;
use App\Domain\Models\Book;
use App\Domain\Models\SearchCriteria;
use App\Domain\Repositories\BookRepositoryInterface;
use PHPUnit\Framework\TestCase;

final class SearchServiceTest extends TestCase
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
        
        $this->searchService = new SearchService(
            $this->mockRepository,
            $this->validator
        );
    }

    /**
     * Тестирует, что поиск возвращает корректный ответ
     */
    public function testSearchReturnsCorrectResponse(): void
    {
        // Arrange
        $request = new SearchRequest(
            query: 'test query',
            category: 'Fiction',
            maxPrice: 1000,
            inStock: true
        );

        $expectedBooks = [
            new Book('1', 'Test Book 1', 'Fiction', 500, []),
            new Book('2', 'Test Book 2', 'Fiction', 800, [])
        ];

        $this->mockRepository
            ->expects($this->once())
            ->method('search')
            ->with($this->callback(function (SearchCriteria $criteria) {
                return $criteria->getQuery() === 'test query' &&
                       $criteria->getCategory() === 'Fiction' &&
                       $criteria->getMaxPrice() === 1000 &&
                       $criteria->isInStock() === true;
            }))
            ->willReturn($expectedBooks);

        // Act
        $response = $this->searchService->search($request);

        // Assert
        $this->assertInstanceOf(SearchResponse::class, $response);
        $this->assertEquals($expectedBooks, $response->getBooks());
        $this->assertEquals(2, $response->getTotal());
        $this->assertGreaterThan(0, $response->getTook());
    }

    /**
     * Тестирует поиск с пустым запросом
     */
    public function testSearchWithEmptyRequest(): void
    {
        // Arrange
        $request = new SearchRequest();

        $this->mockRepository
            ->expects($this->once())
            ->method('search')
            ->with($this->callback(function (SearchCriteria $criteria) {
                return $criteria->getQuery() === null &&
                       $criteria->getCategory() === null &&
                       $criteria->getMaxPrice() === null &&
                       $criteria->isInStock() === false;
            }))
            ->willReturn([]);

        // Act
        $response = $this->searchService->search($request);

        // Assert
        $this->assertInstanceOf(SearchResponse::class, $response);
        $this->assertEquals([], $response->getBooks());
        $this->assertEquals(0, $response->getTotal());
        $this->assertGreaterThan(0, $response->getTook());
    }

    /**
     * Тестирует поиск с null значениями
     */
    public function testSearchWithNullValues(): void
    {
        // Arrange
        $request = new SearchRequest(
            query: null,
            category: null,
            maxPrice: null,
            inStock: false
        );

        $this->mockRepository
            ->expects($this->once())
            ->method('search')
            ->willReturn([]);

        // Act
        $response = $this->searchService->search($request);

        // Assert
        $this->assertInstanceOf(SearchResponse::class, $response);
        $this->assertEquals([], $response->getBooks());
        $this->assertEquals(0, $response->getTotal());
    }

    /**
     * Тестирует измерение времени выполнения поиска
     */
    public function testSearchMeasuresExecutionTime(): void
    {
        // Arrange
        $request = new SearchRequest(query: 'test');

        $this->mockRepository
            ->expects($this->once())
            ->method('search')
            ->willReturn([]);

        // Act
        $response = $this->searchService->search($request);

        // Assert
        $this->assertGreaterThanOrEqual(0, $response->getTook());
        $this->assertLessThan(1000, $response->getTook());
    }
}
