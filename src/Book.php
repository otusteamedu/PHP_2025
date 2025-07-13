<?php

namespace Elisad5791\Phpapp;

use Elastic\Elasticsearch\Client;

class Book {
    const INDEX_NAME = 'books';

    private $elasticClient;

    public function __construct(Client $elasticClient) {
        $this->elasticClient = $elasticClient;
    }

    public function add(array $data) {
        $params = ['body' => []];

        foreach ($data as $item) {
            $id = $item['id'];
            unset($item['id']);
            $params['body'][] = ['index' => ['_index' => self::INDEX_NAME, '_id' => $id]];
            $params['body'][] = $item;
        }

        return $this->elasticClient->bulk($params);
    }
}