<?php

namespace App\Indexes;

class OtusShopIndex extends Index
{
    /** @var string */
    public string $name = 'otus-shop';

    /**
     * @return array
     */
    public function settings(): array {
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
                    'title' => [
                        'type' => 'text',
                        'analyzer' => 'my_russian'
                    ],
                    'sku' => ['type' => 'text'],
                    'category' => ['type' => 'keyword'],
                    'price' => ['type' => 'integer'],
                    'stock' => [
                        'type' => 'nested',
                        'properties' => [
                            'shop' => ['type' => 'keyword'],
                            'stock' => ['type' => 'short'],
                        ]
                    ],
                ],
            ],
        ];
    }
}