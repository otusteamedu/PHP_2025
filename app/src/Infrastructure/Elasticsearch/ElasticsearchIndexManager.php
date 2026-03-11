<?php
declare(strict_types=1);

namespace Pryaniki\App\Infrastructure\Elasticsearch;

use Elastic\Elasticsearch\Client;
use Pryaniki\App\Application\Services\SearchIndexManagerInterface;

class ElasticsearchIndexManager implements SearchIndexManagerInterface
{
    const INDEX_NAME = 'otus-shop';
    private Client $client;
    public function __construct(Client $client)
    {
        $this->client = $client;
    }
    public function createIndex(): void
    {
        $isExists = $this->client
            ->indices()
            ->exists(['index' => self::INDEX_NAME])
            ->asBool();
        if (!$isExists) {
            $config = [];
            $config['index'] = self::INDEX_NAME;
            $config['body'] = [
                'mappings' => $this->getMappingArray(),
                'settings' => $this->getSettingsArray()
            ];

            $this->client
                ->indices()
                ->create($config);
        }
    }

    public function resetIndex(): void
    {
        if ($this->client->indices()->exists(['index' => self::INDEX_NAME])->asBool()) {
            $this->client->indices()->delete([
                'index' => self::INDEX_NAME
            ]);
        }

        $this->createIndex();
    }

    private function getSettingsArray(): array
    {
        return [
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
        ];
    }

    private function getMappingArray(): array
    {
        return [
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
                        'shop' => [
                            'type' => 'keyword'
                        ],
                        'stock' => [
                            'type' => 'integer'
                        ]
                    ]
                ]
            ]
        ];
    }
}