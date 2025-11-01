<?php

namespace App\Index;

class CustomIndexConfig implements IndexConfigInterface
{
    private string $name;
    private array $params;

    public function __construct(string $name, array $additionalParams = [])
    {
        $this->name = $name;
        
        // Base configuration
        $this->params = [
            'index' => $name,
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
        
        // Merge with additional parameters
        $this->params = array_merge_recursive($this->params, $additionalParams);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getParams(): array
    {
        return $this->params;
    }
}