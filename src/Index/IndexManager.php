<?php

namespace App\Index;

use App\Service\ElasticsearchClient;
use Elastic\Elasticsearch\Client;

class IndexManager
{
    private Client $client;

    public function __construct(ElasticsearchClient $elasticsearchClient)
    {
        $this->client = $elasticsearchClient->getClient();
    }

    /**
     * @param IndexConfigInterface $indexConfig
     * @return void
     * @throws \Exception
     */
    public function createIndex(IndexConfigInterface $indexConfig): void
    {
        try {
            $this->client->indices()->create($indexConfig->getParams());
        } catch (\Exception $e) {
            throw new \Exception("Ошибка при создании индекса: " . $e->getMessage());
        }
    }

    /**
     * @param string $indexName
     * @return bool
     * @throws \Exception
     */
    public function indexExists(string $indexName): bool
    {
        try {
            return $this->client->indices()->exists(['index' => $indexName])->asBool();
        } catch (\Exception $e) {
            throw new \Exception("Ошибка при проверке существования индекса: " . $e->getMessage());
        }
    }

    /**
     * @param string $indexName
     * @return void
     * @throws \Exception
     */
    public function deleteIndex(string $indexName): void
    {
        try {
            $this->client->indices()->delete(['index' => $indexName]);
        } catch (\Exception $e) {
            throw new \Exception("Ошибка при удалении индекса: " . $e->getMessage());
        }
    }
}