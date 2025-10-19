<?php

declare(strict_types=1);


namespace Dinargab\Homework12\Index;

use Dinargab\Homework12\Configuration;
use Elastic\Elasticsearch\Client;

class IndexManager
{
    private Client $client;
    private Configuration $config;

    public function __construct(Client $client, Configuration $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * Create ElasticSearch index with mapping
     * 
     * @param bool $recreate Whether to recreate index if it exists
     * @return void
     */
    public function createIndex(bool $recreate = false): void
    {
        $indexName = $this->config->getIndexName();

        if ($recreate && $this->client->indices()->exists(['index' => $indexName])) {
            $this->client->indices()->delete(['index' => $indexName]);
        }

        // Создаём с маппингом поэтому подойдет только этот файл с БД
        $this->client->indices()->create([
            "index" => $indexName,
            "body" => [
                "mappings" => [
                    "properties" => [
                        "title" => ["type" => "text"],
                        "sku" => ["type" => "text"],
                        "category" => ["type" => "keyword"],
                        "price" => ["type" => "integer"],
                        "stock" => [
                            "type" => "nested",
                            "properties" => [
                                "shop" => ["type" => "keyword"],
                                "stock" => ["type" => "integer"]
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }
}