<?php

namespace App\Service;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use RuntimeException;

/**
 * Основной класс ES
 */
class ElasticService
{
    private Client $client;
    private string $index = 'otus-shop';

    /**
     * Подключение к ES
     */
    public function __construct()
    {
        // Внутри Docker-сети обращаемся к сервису ES
        $this->client = ClientBuilder::create()
            ->setHosts(['http://elasticsearch:9200'])
            ->build();
    }

    /**
     * Создает индекс с русской морфологией для поиска
     */
    public function createIndex(): void
    {
        $params = [
            'index' => $this->index,
            'body' => [
                'settings' => [
                    'analysis' => [
                        'filter' => [
                            'russian_stop' => [
                                'type' => 'stop',
                                'stopwords' => '_russian_'
                            ],
                            'russian_stemmer' => [
                                'type' => 'stemmer',
                                'language' => 'russian'
                            ]
                        ],
                        'analyzer' => [
                            'russian' => [
                                'tokenizer' => 'standard',
                                'filter' => [
                                    'lowercase',
                                    'russian_stop',
                                    'russian_stemmer'
                                ]
                            ]
                        ]
                    ]
                ],
                'mappings' => [
                    'properties' => [
                        'title' => [
                            'type' => 'text',
                            'analyzer' => 'russian'
                        ],
                        'sku' => ['type' => 'keyword'],
                        'category' => ['type' => 'keyword'],
                        'price' => ['type' => 'integer'],
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

        // Удаляем старый индекс, если существует
        if ($this->client->indices()->exists(['index' => $this->index])->asBool()) {
            $this->client->indices()->delete(['index' => $this->index]);
        }

        $this->client->indices()->create($params);
    }

    /**
     * Загружает данные из JSON файла в ES
     */
    public function importData(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new RuntimeException("Файл не найден: $filePath");
        }

        $handle = fopen($filePath, 'r');
        $batch = [];
        $counter = 0;

        while (($line = fgets($handle)) !== false) {
            $batch[] = $line;
            
            // Отправляем пакет по 500 записей
            if (count($batch) >= 1000) {
                $this->sendBatch($batch);
                $batch = [];
                $counter += 500;
                echo "Загружено: $counter записей\n";
            }
        }

        if (!empty($batch)) {
            $this->sendBatch($batch);
        }

        fclose($handle);
    }

    /**
     * Отправляет пакет данных в ES
     */
    private function sendBatch(array $batch): void
    {
        $params = ['body' => []];
        
        foreach ($batch as $line) {
            $params['body'][] = json_decode(trim($line), true);
        }

        $this->client->bulk($params);
    }

    /**
     * Выполняет поиск книг с учетом опечаток и фильтров
     */
    public function search(string $query, ?string $category = null, ?int $maxPrice = null, bool $inStock = true): array
    {
        $must = [];
        $filter = [];

        // Поиск с исправлением опечаток
        if (!empty($query)) {
            $must[] = [
                'multi_match' => [
                    'query' => $query,
                    'fields' => ['title'],
                    'fuzziness' => 'AUTO',
                    'analyzer' => 'russian'
                ]
            ];
        }

        // Фильтр по категории
        if ($category) {
            $filter[] = ['term' => ['category' => $category]];
        }

        // Фильтр по цене
        if ($maxPrice !== null) {
            $filter[] = ['range' => ['price' => ['lte' => $maxPrice]]];
        }

        // Фильтр по наличию
        if ($inStock) {
            $filter[] = [
                'nested' => [
                    'path' => 'stock',
                    'query' => [
                        'range' => ['stock.stock' => ['gt' => 0]]
                    ]
                ]
            ];
        }

        $body = [
            'query' => [
                'bool' => [
                    'must' => $must,
                    'filter' => $filter
                ]
            ],
            'sort' => [
                '_score' => ['order' => 'desc'], // Сначала по релевантности
                'price' => ['order' => 'asc']    // Потом по цене
            ],
            'size' => 50
        ];

        $response = $this->client->search([
            'index' => $this->index,
            'body' => $body
        ]);

        return $response->asArray();
    }

    /**
     * Проверяет доступность ES
     */
    public function ping(): bool
    {
        return $this->client->ping()->asBool();
    }
}