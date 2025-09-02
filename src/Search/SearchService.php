<?php

namespace App\Search;

use App\Service\ElasticsearchClient;
use Elastic\Elasticsearch\Client;

class SearchService
{
    private Client $client;

    public function __construct(ElasticsearchClient $elasticsearchClient)
    {
        $this->client = $elasticsearchClient->getClient();
    }

    /**
     * @param array $searchParams
     * @return array
     * @throws \Exception
     */
    public function search(array $searchParams): array
    {
        try {
            return $this->client->search($searchParams)->asArray();
        } catch (\Exception $e) {
            throw new \Exception("Ошибка поиска: " . $e->getMessage());
        }
    }
}