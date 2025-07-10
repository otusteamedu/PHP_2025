<?php declare(strict_types=1);

namespace EManager\Storage;

use Elastic\Elasticsearch\ClientBuilder;

class ElasticsearchStorage implements StorageInterface
{
    private \Elastic\Elasticsearch\Client $client;
    private string $indexName = 'events';

    public function __construct(array $hosts = ['elasticsearch:9200'])
    {
        $this->client = ClientBuilder::create()
            ->setHosts($hosts)
            ->build();

        // Создаем индекс если не существует
        $this->ensureIndexExists();
    }

    private function ensureIndexExists(): void
    {
        $params = ['index' => $this->indexName];

        if (!$this->client->indices()->exists($params)) {
            $this->client->indices()->create([
                'index' => $this->indexName,
                'body' => [
                    'mappings' => [
                        'properties' => [
                            'priority' => ['type' => 'integer'],
                            'conditions' => ['type' => 'object'],
                            'event' => ['type' => 'object']
                        ]
                    ]
                ]
            ]);
        }
    }

    public function addEvent(array $event): void
    {
        $params = [
            'index' => $this->indexName,
            'body' => $event
        ];

        $this->client->index($params);

        // Для немедленного поиска (в production лучше использовать refresh_interval)
        $this->client->indices()->refresh(['index' => $this->indexName]);
    }

    public function clearEvents(): void
    {
        $this->client->deleteByQuery([
            'index' => $this->indexName,
            'body' => [
                'query' => [
                    'match_all' => new \stdClass()
                ]
            ]
        ]);
    }

    public function findMatchingEvent(array $matching): ?array
    {
        if (!isset($matching["params"]) || !is_array($matching['params']))
        {
            return null;
        }

        // Строим запрос для поиска по условиям
        $mustConditions = [];
        foreach ($matching["params"] as $key => $value) {
            $mustConditions[] = ['term' => ["conditions.$key" => $value]];
        }

        $searchParams = [
            'index' => $this->indexName,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => $mustConditions
                    ]
                ],
                'sort' => [
                    ['priority' => ['order' => 'desc']]
                ],
                'size' => 1
            ]
        ];

        $response = $this->client->search($searchParams);

        if ($response['hits']['total']['value'] > 0) {
            return $response['hits']['hits'][0]['_source'];
        }

        return null;
    }
}