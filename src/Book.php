<?php

namespace Elisad5791\Phpapp;

use Elastic\Elasticsearch\Client;

class Book 
{
    const INDEX_NAME = 'books';

    private $elasticClient;

    public function __construct(Client $elasticClient) {
        $this->elasticClient = $elasticClient;
    }

    public function getById(string $id): array
    {
        $params = ['index' => self::INDEX_NAME, 'id' => $id];
        $result = $this->elasticClient->get($params)->asArray();
        return $result;
    }

    public function getBySubjectId(string $id): array
    {
        $params = [
            'index' => self::INDEX_NAME,
            'body'  => [
                'query' => [
                    'match' => [
                        'subject_id' => $id
                    ]
                ]
            ]
        ];

        $result = $this->elasticClient->search($params)->asArray();
        return $result;
    }

    public function searchByTitle(string $query): array
    {
        $params = [
            'index' => self::INDEX_NAME,
            'body'  => [
                'query' => [
                    'match' => [
                        'title' => $query
                    ]
                ]
            ]
        ];

        $result = $this->elasticClient->search($params)->asArray();
        return $result;
    }

    public function searchByDescription(string $query): array
    {
        $params = [
            'index' => self::INDEX_NAME,
            'body'  => [
                'query' => [
                    'match' => [
                        'description' => $query
                    ]
                ]
            ]
        ];

        $result = $this->elasticClient->search($params)->asArray();
        return $result;
    }

    public function add(array $data): void 
    {
        $params = ['body' => []];

        foreach ($data as $item) {
            $id = $item['id'];
            unset($item['id']);
            $params['body'][] = ['index' => ['_index' => self::INDEX_NAME, '_id' => $id]];
            $params['body'][] = $item;
        }

        $this->elasticClient->bulk($params);
    }

    public function delete(string $id): void
    {
        $params = ['index' => self::INDEX_NAME, 'id' => $id];
        $this->elasticClient->delete($params);
    }
}