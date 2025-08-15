<?php

declare(strict_types=1);

namespace User\Php2025\Elasticsearch;

class ElasticSearch
{
    private ElasticClient $elasticClient;

    private array $parameters;

    public function __construct(ElasticClient $elasticClient)
    {
        $this->elasticClient = $elasticClient;
        $this->parameters = [
            'body' => [
                'query' => [
                    'bool' => []
                ]
            ]
        ];
    }

    public function search(?string $title, ?string $price): void
    {
        $elasticIndex = new ElasticIndex($this->elasticClient);
        if ($elasticIndex->indexExists() === false) {
            echo 'Идекс не существует. Создайте пожалуйста индекс.';
            exit();
        }

        $this->setTitleParams($title);
        $this->setPriceParams($price);
        $this->setStockParams();

        $client = $this->elasticClient->getClient();
        $indexName = new ElasticInfo();

        try {
            $response = $client->search([
                'index' => $indexName->getIndexName(),
                'body' => $this->parameters['body'],
            ]);

            foreach ($response['hits']['hits'] as $hit) {
                echo 'Title: ' . $hit['_source']['title'] . ' Price: ' . $hit['_source']['price'] . PHP_EOL;
            }
        } catch (\Exception $exception) {
            echo 'Ошибка: ' . $exception->getMessage() . PHP_EOL;

        }
    }

    private function setTitleParams(?string $title): void
    {
        if ($title !== null) {
            $this->parameters['body']['query']['bool']['must'][] = [
                'match' => [
                    'title' => [
                        'query' => $title,
                        'fuzziness' => 'auto'
                    ]
                ],
            ];
        }
    }

    private function setPriceParams($price): void
    {
        if ($price !== null) {
            $this->parameters['body']['query']['bool']['filter'][] = [
                'range' => [
                    'price' => [
                        'lt' => $price,
                    ]
                ]
            ];
        }
    }

    private function setStockParams(): void
    {
        $this->parameters['body']['query']['bool']['filter'][] = [
            'nested' => [
                'path' => 'stock',
                'query' => [
                    'bool' => [
                        'filter' => [
                            'range' => [
                                'stock.stock' => [
                                    'gt' => 0,
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
