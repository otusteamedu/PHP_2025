<?php

declare(strict_types=1);

namespace App\Infrastructure\Elasticsearch;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Exception;

final class ElasticsearchClient
{
    /** Название индекса для книг */
    private const string INDEX_NAME = 'books';

    /** Клиент для работы с Elasticsearch */
    private Client $client;

    public function __construct() 
    {
        $host = $_ENV['ELASTICSEARCH_HOST'] ?? 'elasticsearch';
        $port = (int) ($_ENV['ELASTICSEARCH_PORT'] ?? 9200);
        
        $this->client = ClientBuilder::create()
            ->setHosts(["{$host}:{$port}"])
            ->build();
    }

    /**
     * Возвращает клиент Elasticsearch
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * Возвращает название индекса для книг
     */
    public function getIndexName(): string
    {
        return self::INDEX_NAME;
    }

    /**
     * Проверяет доступность Elasticsearch
     */
    public function isConnected(): bool
    {
        try {
            $this->client->ping();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Возвращает настройки индекса с русской морфологией
     */
    public function getIndexSettings(): array
    {
        return [
            'settings' => [
                'analysis' => [
                    'analyzer' => [
                        'russian_analyzer' => [
                            'type' => 'russian'
                        ],
                        'title_analyzer' => [
                            'type' => 'custom',
                            'tokenizer' => 'standard',
                            'filter' => [
                                'lowercase',
                                'russian_stop',
                                'russian_stemmer'
                            ]
                        ]
                    ],
                    'filter' => [
                        'russian_stop' => [
                            'type' => 'stop',
                            'stopwords' => '_russian_'
                        ],
                        'russian_stemmer' => [
                            'type' => 'stemmer',
                            'language' => 'russian'
                        ]
                    ]
                ]
            ],
            'mappings' => [
                'properties' => [
                    'title' => [
                        'type' => 'text',
                        'analyzer' => 'title_analyzer',
                        'search_analyzer' => 'title_analyzer',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword'
                            ]
                        ]
                    ],
                    'category' => [
                        'type' => 'text',
                        'analyzer' => 'russian_analyzer',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword'
                            ]
                        ]
                    ],
                    'price' => [
                        'type' => 'float'
                    ],
                    'stock' => [
                        'type' => 'nested',
                        'properties' => [
                            'shop' => [
                                'type' => 'keyword'
                            ],
                            'stock' => [
                                'type' => 'integer'
                            ]
                        ]
                    ],
                    'total_stock' => [
                        'type' => 'integer'
                    ]
                ]
            ]
        ];
    }
}