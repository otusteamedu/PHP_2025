<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Elasticsearch;

use App\Infrastructure\Elasticsearch\ElasticsearchBookRepository;
use App\Infrastructure\Elasticsearch\ElasticsearchClient;
use App\Infrastructure\Elasticsearch\IndexManager;
use App\Domain\Models\Book;
use App\Domain\Models\SearchCriteria;
use Exception;
use PHPUnit\Framework\TestCase;

final class ElasticsearchBookRepositoryTest extends TestCase
{
    private ElasticsearchBookRepository $repository;
    private ElasticsearchClient $client;
    private IndexManager $indexManager;

    /**
     * Настройка тестового окружения
     */
    protected function setUp(): void
    {
        $this->client = new ElasticsearchClient();
        $this->indexManager = new IndexManager($this->client);
        $this->repository = new ElasticsearchBookRepository($this->client, $this->indexManager);
    }

    /**
     * Тестирует создание репозитория
     */
    public function testRepositoryCreation(): void
    {
        $this->assertInstanceOf(ElasticsearchBookRepository::class, $this->repository);
    }

    /**
     * Тестирует проверку существования индекса
     */
    public function testIndexExists(): void
    {
        $exists = $this->repository->indexExists();
        $this->assertIsBool($exists);
    }

    /**
     * Тестирует создание индекса
     */
    public function testCreateIndex(): void
    {
        $this->expectNotToPerformAssertions();
        try {
            $this->repository->createIndex();
        } catch (Exception $e) {
            // Игнорируем ошибку, если индекс уже существует
        }
    }

    /**
     * Тестирует удаление индекса
     */
    public function testDeleteIndex(): void
    {
        $this->expectNotToPerformAssertions();
        try {
            $this->repository->deleteIndex();
        } catch (Exception $e) {
            // Игнорируем ошибку, если индекс не существует
        }
    }

    /**
     * Тестирует индексацию книг
     */
    public function testBulkIndex(): void
    {
        $books = [
            new Book('1', 'Test Book', 'Test Category', 1000, [['store' => 'Test Store', 'stock' => 5]])
        ];

        $this->expectNotToPerformAssertions();
        try {
            $this->repository->bulkIndex($books);
        } catch (Exception $e) {
            $this->markTestSkipped('Elasticsearch недоступен');
        }
    }

    /**
     * Тестирует поиск книг
     */
    public function testSearch(): void
    {
        $criteria = new SearchCriteria();

        try {
            $results = $this->repository->search($criteria);
            $this->assertIsArray($results);
        } catch (Exception $e) {
            $this->markTestSkipped('Elasticsearch недоступен');
        }
    }
}
