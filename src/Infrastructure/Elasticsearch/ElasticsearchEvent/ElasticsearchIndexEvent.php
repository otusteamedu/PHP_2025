<?php

namespace App\Infrastructure\Elasticsearch\ElasticsearchEvent;

use App\Infrastructure\Elasticsearch\ElasticsearchManager;

class ElasticsearchIndexEvent extends ElasticsearchManager
{
    private const INDEX_NAME = 'otus-event';

    public function __construct()
    {
        parent::__construct(self::INDEX_NAME);
    }

    /**
     * Настройки для индекса
     */
    protected function getSettings(): array
    {
        return [
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
                                'lowercase', 'ru_stop', 'ru_stemmer'
                            ]
                        ]
                    ]
                ]
            ],
            'mappings' => [
                'properties' => [
                    'priority' => ['type' => 'rank_feature'],
                    'event' => [
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'name' => ['type' => 'text', 'analyzer' => 'my_russian'],
                        ]
                    ],
                    'conditions' => [
                        'type' => 'nested',
                        'properties' => [
                            'conditionName' => ['type' => 'keyword'],
                            'conditionValue' => ['type' => 'keyword'],
                        ]
                    ],
                ],
            ],
        ];
    }
}
