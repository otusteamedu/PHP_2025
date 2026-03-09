<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Infrastructure\Elasticsearch\Client\ElasticsearchClientProvider;
use Elastic\Elasticsearch\Client;

class BookshopRepository
{
    private readonly Client $esClient;

    public function __construct()
    {
        $this->esClient = ElasticsearchClientProvider::get();
    }

    public function createIndex(string $indexName, array $params = []): bool
    {
        return $this->esClient->indices()->create(['index' => $indexName, 'body' => $params])->asBool();
    }

    public function deleteIndex(string $indexName): bool
    {
        return $this->esClient->indices()->delete(['index' => $indexName])->asBool();
    }

    public function existsIndex(string $indexName): bool
    {
        return $this->esClient->indices()->exists(['index' => $indexName])->asBool();
    }

    public function loadBulkDocuments(array $data): bool
    {
        return $this->esClient->bulk(['body' => $data])->asBool();
    }
}
