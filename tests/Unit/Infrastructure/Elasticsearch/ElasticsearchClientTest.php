<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Elasticsearch;

use App\Infrastructure\Elasticsearch\ElasticsearchClient;
use PHPUnit\Framework\TestCase;

final class ElasticsearchClientTest extends TestCase
{
    private ElasticsearchClient $client;

    /**
     * Настройка тестового окружения
     */
    protected function setUp(): void
    {
        $this->client = new ElasticsearchClient();
    }

    /**
     * Тестирует создание клиента
     */
    public function testClientCreation(): void
    {
        $this->assertInstanceOf(ElasticsearchClient::class, $this->client);
    }

    /**
     * Тестирует получение клиента Elasticsearch
     */
    public function testGetClient(): void
    {
        $client = $this->client->getClient();
        $this->assertNotNull($client);
    }
}
