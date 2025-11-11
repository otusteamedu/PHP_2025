<?php
namespace App\Repository;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;

class ElasticEventRepository implements EventRepositoryInterface {
    private Client $elastic;
    private string $index = 'events';

    public function __construct(Client $elastic) {
        $this->elastic = $elastic;

        if (!$this->elastic->indices()->exists(['index' => $this->index])->asBool()) {
            $this->elastic->indices()->create(['index' => $this->index]);
        }
    }

    public function addEvent(array $event): void {
        $this->elastic->index([
            'index' => $this->index,
            'body' => $event
        ]);
    }

    public function clearEvents(): void {
        $this->elastic->deleteByQuery([
            'index' => $this->index,
            'body' => ['query' => ['match_all' => (object)[]]]
        ]);
    }

    public function findMatchingEvent(array $params): ?array {
        $must = [];
        foreach ($params as $key => $value) {
            $must[] = ['match' => ["conditions.$key" => $value]];
        }

        $response = $this->elastic->search([
            'index' => $this->index,
            'body' => [
                'query' => [
                    'bool' => ['must' => $must]
                ],
                'sort' => [['priority' => ['order' => 'desc']]],
                'size' => 1
            ]
        ]);

        $hits = $response->asArray()['hits']['hits'] ?? [];
        return $hits[0]['_source'] ?? null;
    }
}
