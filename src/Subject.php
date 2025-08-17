<?php

namespace Elisad5791\Phpapp;

class Subject 
{
    const INDEX_NAME = 'subjects';
    

    public function __construct(
        private ElasticSearchClientInterface $elasticClient
    ) {}

    public function getById(string $id): array
    {
        $params = ['index' => self::INDEX_NAME, 'id' => $id];
        $result = $this->elasticClient->get($params);
        return $result;
    }

    public function add(array $data):void 
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