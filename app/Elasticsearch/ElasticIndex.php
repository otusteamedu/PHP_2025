<?php

declare(strict_types=1);

namespace User\Php2025\Elasticsearch;

class ElasticIndex
{
    private ElasticClient $client;

    public function __construct(ElasticClient $client)
    {
        $this->client = $client;
    }

    public function createIndex(): void
    {
        $indexName = new ElasticInfo();

        try {
            $client = $this->client->getClient();

            $params = [
                'index' => $indexName->getIndexName(),
                'body' => $indexName->getMappingBody(),
            ];

            $response = $client->indices()->create($params);

            echo 'Индекс ' . $response['index'] . ' добавлен.';
        } catch (\Exception) {

            echo 'Индекс ' . $indexName->getIndexName() . ' уже существует.';
        }
    }

    public function deleteIndex(): void
    {
        $indexName = new ElasticInfo();

        try {
            $client = $this->client->getClient();

            $params = [
                'index' => $indexName->getIndexName(),
            ];

            $client->indices()->delete($params);

            echo 'Индекс ' . $indexName->getIndexName() . ' удален.';
        } catch (\Exception) {

            echo 'Индекса ' . $indexName->getIndexName() . ' не существует.';
        }
    }

    public function indexExists(): bool
    {
        $indexName = new ElasticInfo();
        $client = $this->client->getClient();

        return $client->indices()->exists(['index' => $indexName->getIndexName()])->asBool();
    }
}
