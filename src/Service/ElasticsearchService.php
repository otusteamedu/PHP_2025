<?php declare(strict_types=1);

namespace App\Service;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;

class ElasticsearchService
{
    private Client $client;

    public function __construct(array $hosts = ['localhost:9200'])
    {
        $this->client = ClientBuilder::create()
            ->setHosts($hosts)
            ->build();
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function createIndex(string $indexName, string $filePath): array
    {
        if ($this->indexExists($indexName)) {
            throw new \RuntimeException('Index already exists');
        }

        if (!file_exists($filePath)) {
            throw new \RuntimeException('Mapping file not found');
        }

        $mappingJson = file_get_contents($filePath);
        $mappingData = json_decode($mappingJson, true);

        if (!$mappingData) {
            throw new \RuntimeException('Invalid mapping file');
        }

        $params = [
            'index' => $indexName,
            'body' => $mappingData
        ];

        return $this->client->indices()->create($params)->asArray();
    }

    public function deleteIndex(string $indexName): void
    {
        if (!$this->indexExists($indexName)) {
            throw new \RuntimeException('Index does not exist');
        }

        $this->client->indices()->delete(['index' => $indexName]);
    }

    public function indexExists(string $indexName): bool
    {
        return $this->client->indices()->exists(['index' => $indexName])->asBool();
    }

    public function bulkInsertFromJson(string $jsonFilePath): void
    {
        if (!file_exists($jsonFilePath)) {
            throw new \RuntimeException("Файл не найден: $jsonFilePath");
        }

        $bulkData = file_get_contents($jsonFilePath);
        if (!$bulkData) {
            throw new \RuntimeException("Ошибка чтения файла: $jsonFilePath");
        }

        $this->client->bulk(['body' => $bulkData]);
    }

    public function scrollSearch(string $index, int $batchSize = 1000): array
    {
        $allDocuments = [];

        $params = [
            'index' => $index,
            'body' => [
                'size' => $batchSize,
                'query' => ['match_all' => new \stdClass()]
            ],
            'scroll' => '1m'
        ];

        $response = $this->client->search($params)->asArray();

        if (!isset($response['hits']['hits']) || empty($response['hits']['hits'])) {
            return [];
        }

        $allDocuments = array_merge($allDocuments, $response['hits']['hits']);
        $scrollId = $response['_scroll_id'] ?? null;

        while ($scrollId) {
            $scrollResponse = $this->client->scroll([
                'scroll_id' => $scrollId,
                'scroll' => '1m'
            ])->asArray();

            if (empty($scrollResponse['hits']['hits'])) {
                break;
            }

            $allDocuments = array_merge($allDocuments, $scrollResponse['hits']['hits']);
            $scrollId = $scrollResponse['_scroll_id'] ?? null;
        }

        if ($scrollId) {
            $this->client->clearScroll(['scroll_id' => $scrollId]);
        }

        return $allDocuments;
    }
}
