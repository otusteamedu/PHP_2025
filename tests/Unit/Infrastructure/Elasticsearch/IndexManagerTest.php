<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Elasticsearch;

use App\Infrastructure\Elasticsearch\IndexManager;
use App\Infrastructure\Elasticsearch\ElasticsearchClient;
use PHPUnit\Framework\TestCase;

final class IndexManagerTest extends TestCase
{
    private IndexManager $indexManager;
    private ElasticsearchClient $client;

    /**
     * Настройка тестового окружения
     */
    protected function setUp(): void
    {
        $this->client = new ElasticsearchClient();
        $this->indexManager = new IndexManager($this->client);
    }

    /**
     * Тестирует создание IndexManager
     */
    public function testIndexManagerCreation(): void
    {
        $this->assertInstanceOf(IndexManager::class, $this->indexManager);
    }

    /**
     * Тестирует проверку существования индекса
     */
    public function testIndexExists(): void
    {
        $exists = $this->indexManager->indexExists();
        $this->assertIsBool($exists);
    }
}
