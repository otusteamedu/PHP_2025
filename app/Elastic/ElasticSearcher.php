<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025\Elastic;

use Exception;
use RuntimeException;

class ElasticSearcher
{

    private ElasticClient $elastic;
    private array $arParameter;

    public function __construct(ElasticClient $elastic)
    {
        $this->elastic = $elastic;

        $this->arParameter = [
            'body' => [
                'query' => [
                    'bool' => []
                ]
            ]
        ];
    }

    public function search(string $title, float $price, string $index): void
    {
        $this->setIndexParameter($index);
        $this->setTitleParameter($title);
        $this->setPriceParameter($price);

        $client = $this->elastic->getClient();

        try {
            $response = $client->search($this->arParameter);

            if (!empty($response['hits']['hits'])) {
                echo 'Результат поиска: ' . PHP_EOL;
                foreach ($response['hits']['hits'] as $hit) {
                    echo $hit['_source']['title'] . ' - ' . $hit['_source']['price'] . ' руб.';
                }
            } else {
                echo 'Ничего не найдено!' . PHP_EOL;
            }
        } catch (Exception $e) {
            $requestInfo = $client->transport->getLastConnection()->getLastRequestInfo();

            if (isset($requestInfo['response']) && ($response = $requestInfo['response'])) {
                if (isset($response['error']) && ($error = $response['error']) && $error instanceof RuntimeException) {
                    echo $error->getMessage();
                }

                if (!empty($response['reason'])) {
                    echo $response['reason'];
                }
            } else {
                echo 'Ошибка Elasticsearch: ' . $e->getMessage();
            }
        }
    }

    private function setIndexParameter(string $index): void
    {
        $this->arParameter['index'] = $index;
    }

    private function setTitleParameter(string $title): void
    {
        if (!empty($title)) {
            $this->arParameter['body']['query']['bool']['must'] = [
                [
                    'match' => [
                        'title' => [
                            'query' => $title,
                            'fuzziness' => 'AUTO'
                        ]
                    ],
                ]
            ];
            $this->arParameter['body']['query']['bool']['filter'][] = [
                'nested' => [
                    'path' => 'stock',
                    'query' => [
                        'range' => [
                            'stock.stock' => ['gt' => 0]
                        ]
                    ]
                ]
            ];
        }
    }

    private function setPriceParameter(float $price): void
    {
        if (!empty($price)) {
            $this->arParameter['body']['query']['bool']['filter'][] = [
                'range' => [
                    'price' => [
                        'lte' => $price
                    ]
                ]
            ];
        }
    }
}
