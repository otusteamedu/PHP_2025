<?php

namespace Otus\Code\Infrastructure\Elasticsearch;

use Elastic\Elasticsearch\ClientBuilder;
use Exception;

class ElasticSearchService {

    private $client;

    public function __construct() {
        $this->client = ClientBuilder::create()
            ->setHosts([
                $_ENV['ELASTIC_HOST'],
            ])
            ->setSSLVerification(false)
            ->setBasicAuthentication($_ENV['ELASTIC_USER'], $_ENV['ELASTIC_PASSWORD'],)
            ->build();
    }

    public function getClient() {

        return $this->client;
    }

    public function createIndex(string $indexName) {

        // Проверяем, существует ли индекс
        if ($this->client->indices()->exists(['index' => $indexName])->asBool()) {
            $this->client->indices()->delete(['index' => $indexName]);
        }

        // Создаем индекс с маппингом
        $params = [
            'index' => $indexName,
            'body' => [
                'settings' => [
                  'analysis' => [
                    'filter' => [
                        'ru_stop' => [
                            'type' => 'stop',
                            'stopwords' => '_russian_'
                        ],
                        'ru_stemmer' => [
                            'type' => 'stemmer',
                            'language' => 'russian'
                        ]
                    ],
                    'analyzer' => [
                        'my_russian' => [
                            'tokenizer' => 'standard',
                            'filter' => [
                                'lowercase',
                                'ru_stop',
                                'ru_stemmer'
                            ]
                        ]
                    ]
                  ]
                ],
                'mappings' => [
                    'properties' => [
                        'title' => [
                            'type' => 'text',
                            'analyzer' => 'my_russian'
                        ],
                        'sku' => [
                            'type' => 'keyword'
                        ],
                        'category' => [
                            'type' => 'keyword'
                        ],
                        'price' => [
                            'type' => 'integer'
                        ],
                        'stock' => [
                            'type' => 'nested',
                            'properties' => [
                                'shop' => ['type' => 'keyword'],
                                'stock' => ['type' => 'integer']
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->client->indices()->create($params);
    }

    public function downloadDocuments(string $indexName, string $jsonFilePath) {

        $lines = file(__DIR__.$jsonFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        if (!$lines) {
            throw new Exception('Не удалось прочитать файл $jsonFilePath или файл пуст');
        }

        $batchSize = 100; // Размер пачки для bulk-запроса
        $bulkData = [];
        $docCount = 0;

        for ($i = 0; $i < count($lines); $i += 2) {
            // Пропускаем, если недостаточно строк
            if (!isset($lines[$i + 1])) {
                break;
            }

            // Первая строка - метаданные (create operation)
            $meta = json_decode($lines[$i], true);
            // Вторая строка - документ
            $document = json_decode($lines[$i + 1], true);

            // Добавляем в bulk-запрос
            $bulkData['body'][] = [
                'create' => [
                    '_index' => $indexName,
                    '_id' => $meta['create']['_id']
                ]
            ];

            $bulkData['body'][] = $document;
            $docCount++;

            // Отправляем пачку
            if (count($bulkData['body']) >= $batchSize * 2) {
                $response = $this->client->bulk($bulkData);
                
                if ($response['errors']) {
                    throw new Exception('Не удалось загрузить документы в $indexName');
                }
                
                $bulkData = [];
            }
        }

        // Отправляем оставшиеся документы
        if (!empty($bulkData)) {
            $response = $this->client->bulk($bulkData);
            
            if ($response['errors']) {
                throw new Exception('Не удалось загрузить документы в $indexName');
            }
        }

        // Обновляем счетчики документов (опционально)
        $this->client->indices()->refresh(['index' => $indexName]);
        $stats = $this->client->count(['index' => $indexName]);
    }

    public function search(string $indexName, array $search_options, array $map) {
      $params = [
        'index' => $indexName,
        'body'  => [
              'query' => ElasticSearchQuery::getQuery($search_options, $map),
              'size'  => 1000  // размер выборки
        ]
      ];

      return $this->client->search($params);
    }
}